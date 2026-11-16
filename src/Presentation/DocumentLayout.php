<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation;

use Lemonade\EmailGenerator\Document;

/**
 * Fluid shell with a fixed-width MSO fallback; no request or external stylesheet
 *
 * @internal
 */
final class DocumentLayout
{
    /**
     * Wraps rendered rows in a fluid email document with metadata and an Outlook width fallback
     */
    public static function render(Document $document, RenderContext $context, string $rows): string
    {
        $config = $context->config();
        $width = $config->width()->value;
        $lang = RenderContext::escape(str_replace('_', '-', $document->locale()));
        $title = $context->text($document->title());
        $preheader = $context->text($document->preheader());
        $direction = $document->direction()->value;

        return <<<HTML
<!DOCTYPE html>
<html lang="{$lang}" dir="{$direction}" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="x-apple-disable-message-reformatting">
<title>{$title}</title>
<!--[if mso]><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->
</head>
<body style="margin:0;padding:0;width:100%;background-color:{$config->backgroundColor()->hex()};-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">
<div style="display:none;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;" aria-hidden="true">{$preheader}</div>
<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="{$config->backgroundColor()->hex()}" style="width:100%;border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt;">
<tr><td align="center" style="padding:24px 0;">
<!--[if mso]><table role="presentation" align="center" width="{$width}" border="0" cellpadding="0" cellspacing="0"><tr><td><![endif]-->
<table role="presentation" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="{$config->contentColor()->hex()}" dir="{$direction}" style="width:100%;max-width:{$width}px;background-color:{$config->contentColor()->hex()};border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt;">
<tr><td style="padding:32px;color:{$config->textColor()->hex()};font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:24px;">
<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt;">
{$rows}</table>
</td></tr></table>
<!--[if mso]></td></tr></table><![endif]-->
</td></tr></table>
</body>
</html>

HTML;
    }
}
