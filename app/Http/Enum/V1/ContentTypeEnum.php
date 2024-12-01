<?php

namespace App\Http\Enum\V1;

enum ContentTypeEnum: string
{
    case Heading = 'heading';
    case LinkEmbed = 'linkTool';
    case Image = 'image';
    case Paragraph = 'paragraph';
    case Quote = 'quote';
}
