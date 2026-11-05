<?php

namespace FredBradley\SmugMug\Enums;

/**
 * The sizes SmugMug serves an image at, as an image's !size{name} endpoints name them. Which are available depends
 * on the image and on the album's LargestSize.
 */
enum Size: string
{
    case Tiny = 'tiny';
    case Thumb = 'thumb';
    case Small = 'small';
    case Medium = 'medium';
    case Large = 'large';
    case XLarge = 'xlarge';
    case X2Large = 'x2large';
    case X3Large = 'x3large';
    case X4Large = 'x4large';
    case X5Large = 'x5large';
    case FourK = '4k';
    case FiveK = '5k';
    case Original = 'original';
}
