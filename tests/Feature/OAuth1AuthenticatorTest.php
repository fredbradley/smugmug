<?php

use FredBradley\SmugMug\Auth\OAuth1Authenticator;
use FredBradley\SmugMug\Tests\Fixtures\ExampleFormRequest;
use FredBradley\SmugMug\Tests\Helpers\FakeSmugMug;
use GuzzleHttp\Psr7\Request;

/*
 * The example request and signature from Twitter's "Creating a signature" guide, which follows RFC 5849.
 */
function twitterSigner(): OAuth1Authenticator
{
    return new OAuth1Authenticator(
        'xvz1evFS4wEEPTGEFPHBog',
        'kAcSOqF21Fu85e7zjz7ZN2U4ZRhfV3WpwPAoE3Z7kBw',
        '370773112-GmHxMAgYyLbNEtIKZeRNFsMKPR9EyMZeS9weJAEb',
        'LswwdoUaIvS8ltyTt5jkRh4J50vUPVVHtR2YPi5kE',
        nonce: fn () => 'kYjzVBB8Y0ZFabxSWbWovY3uYSQ2pTgmZeNu2VS4cg',
        clock: fn () => 1318622958,
    );
}

it('makes the signature RFC 5849 documents', function () {
    $request = new Request('POST', 'https://api.twitter.com/1.1/statuses/update.json?include_entities=true', ['Content-Type' => 'application/x-www-form-urlencoded'], 'status=Hello%20Ladies%20%2B%20Gentlemen%2C%20a%20signed%20OAuth%20request%21');
    $signed = twitterSigner()->sign($request);

    expect($signed->getHeaderLine('Authorization'))->toBe('OAuth oauth_consumer_key="xvz1evFS4wEEPTGEFPHBog", oauth_nonce="kYjzVBB8Y0ZFabxSWbWovY3uYSQ2pTgmZeNu2VS4cg", oauth_signature_method="HMAC-SHA1", oauth_timestamp="1318622958", oauth_version="1.0", oauth_token="370773112-GmHxMAgYyLbNEtIKZeRNFsMKPR9EyMZeS9weJAEb", oauth_signature="hCtSmYh%2BiHYCEqBWrE7C7hYmtUk%3D"')
        ->and((string) $signed->getBody())->toBe('status=Hello%20Ladies%20%2B%20Gentlemen%2C%20a%20signed%20OAuth%20request%21');
});

it('writes the signature base string RFC 5849 describes', function () {
    $base = OAuth1Authenticator::baseString('post', 'https://api.twitter.com/1.1/statuses/update.json?ignored=1', [
        ['status', 'Hello Ladies + Gentlemen, a signed OAuth request!'],
        ['include_entities', 'true'],
        ['oauth_consumer_key', 'xvz1evFS4wEEPTGEFPHBog'],
        ['oauth_nonce', 'kYjzVBB8Y0ZFabxSWbWovY3uYSQ2pTgmZeNu2VS4cg'],
        ['oauth_signature_method', 'HMAC-SHA1'],
        ['oauth_timestamp', '1318622958'],
        ['oauth_token', '370773112-GmHxMAgYyLbNEtIKZeRNFsMKPR9EyMZeS9weJAEb'],
        ['oauth_version', '1.0'],
    ]);

    expect($base)->toBe('POST&https%3A%2F%2Fapi.twitter.com%2F1.1%2Fstatuses%2Fupdate.json&include_entities%3Dtrue%26oauth_consumer_key%3Dxvz1evFS4wEEPTGEFPHBog%26oauth_nonce%3DkYjzVBB8Y0ZFabxSWbWovY3uYSQ2pTgmZeNu2VS4cg%26oauth_signature_method%3DHMAC-SHA1%26oauth_timestamp%3D1318622958%26oauth_token%3D370773112-GmHxMAgYyLbNEtIKZeRNFsMKPR9EyMZeS9weJAEb%26oauth_version%3D1.0%26status%3DHello%2520Ladies%2520%252B%2520Gentlemen%252C%2520a%2520signed%2520OAuth%2520request%2521');
});

it('sorts repeated parameters by value', function () {
    $base = OAuth1Authenticator::baseString('GET', 'https://api.smugmug.com/api/v2', [['b', '2'], ['a', 'z'], ['a', 'y']]);

    expect($base)->toBe('GET&https%3A%2F%2Fapi.smugmug.com%2Fapi%2Fv2&a%3Dy%26a%3Dz%26b%3D2');
});

it('normalises the URL it signs', function (string $url, string $expected) {
    expect(OAuth1Authenticator::baseUrl($url))->toBe($expected);
})->with([
    ['HTTPS://API.SmugMug.com/api/v2/album/X?count=1#top', 'https://api.smugmug.com/api/v2/album/X'],
    ['https://api.smugmug.com:443/api/v2', 'https://api.smugmug.com/api/v2'],
    ['http://example.com:80/a', 'http://example.com/a'],
    ['https://example.com:8443/a', 'https://example.com:8443/a'],
    ['https://upload.smugmug.com', 'https://upload.smugmug.com/'],
]);

it('refuses to sign a URL that is not absolute', function () {
    expect(fn () => OAuth1Authenticator::baseUrl('/api/v2'))->toThrow(LogicException::class, 'not an absolute URL');
});

it('signs a form body\'s fields, but not a JSON body\'s', function () {
    $signer = new OAuth1Authenticator('key', 'secret', 'token', 'token-secret', nonce: fn () => 'n', clock: fn () => 1);
    $form = $signer->sign(new Request('POST', 'https://api.smugmug.com/a', ['Content-Type' => 'application/x-www-form-urlencoded'], 'Name=x'));
    $json = $signer->sign(new Request('POST', 'https://api.smugmug.com/a', ['Content-Type' => 'application/json'], '{"Name":"x"}'));
    $none = $signer->sign(new Request('POST', 'https://api.smugmug.com/a'));

    expect(FakeSmugMug::oauthParameters($form)['oauth_signature'])->not->toBe(FakeSmugMug::oauthParameters($json)['oauth_signature'])
        ->and(FakeSmugMug::oauthParameters($json)['oauth_signature'])->toBe(FakeSmugMug::oauthParameters($none)['oauth_signature']);
});

it('signs a form request through the connector so SmugMug can check it', function () {
    $mockClient = FakeSmugMug::respondWith([ExampleFormRequest::class => FakeSmugMug::action()]);

    FakeSmugMug::user()->send(new ExampleFormRequest(['Name' => 'Big Cats', 'Privacy' => 'Public']));
    $sent = FakeSmugMug::sent($mockClient, ExampleFormRequest::class)[0];

    expect((string) $sent->getBody())->toBe('Name=Big+Cats&Privacy=Public')
        ->and(FakeSmugMug::signatureIsValid($sent))->toBeTrue();
});

it('signs the steps of the flow: no token, then the request token and verifier', function () {
    $first = new OAuth1Authenticator('key', 'secret', protocolParameters: ['oauth_callback' => 'https://app.test/callback'], nonce: fn () => 'n', clock: fn () => 1);
    $last = new OAuth1Authenticator('key', 'secret', 'request-token', 'request-secret', ['oauth_verifier' => '123456'], fn () => 'n', fn () => 1);

    $one = FakeSmugMug::oauthParameters($first->sign(new Request('GET', 'https://api.smugmug.com/services/oauth/1.0a/getRequestToken')));
    $two = FakeSmugMug::oauthParameters($last->sign(new Request('GET', 'https://api.smugmug.com/services/oauth/1.0a/getAccessToken')));

    expect($one)->not->toHaveKey('oauth_token')
        ->and($one['oauth_callback'])->toBe('https://app.test/callback')
        ->and($first->hasToken())->toBeFalse()
        ->and($two['oauth_token'])->toBe('request-token')
        ->and($two['oauth_verifier'])->toBe('123456')
        ->and($last->hasToken())->toBeTrue();
});

it('takes only oauth_ parameters as protocol parameters', function () {
    expect(fn () => new OAuth1Authenticator('key', 'secret', protocolParameters: ['Access' => 'Full']))->toThrow(LogicException::class, 'not an OAuth protocol parameter');
});

it('never shows or serialises its secrets', function () {
    $signer = new OAuth1Authenticator('key', 'secret', 'token', 'token-secret');

    expect(print_r($signer, true))->not->toContain('token-secret')
        ->and($signer->__debugInfo()['token'])->toBe('[redacted]')
        ->and((new OAuth1Authenticator('key', 'secret'))->__debugInfo()['token'])->toBeNull()
        ->and(fn () => serialize($signer))->toThrow(LogicException::class);
});

it('encodes as RFC 3986 does', function () {
    expect(OAuth1Authenticator::encode('a b+c~d*e!'))->toBe('a%20b%2Bc~d%2Ae%21');
});
