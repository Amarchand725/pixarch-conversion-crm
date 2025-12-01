<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    @section('title', ($title ?? '').' - '. config('app.name', '100 KEYS UAE'))
</head>
<body style="margin:0; padding:0; background:#f5f5f5; font-family:Arial, sans-serif;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background:#f5f5f5; padding: 24px 0;">
        <tr>
            <td align="center">
                
                {{-- Wrapper --}}
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius: 8px; overflow:hidden;">
                    
                    {{-- Header / Logo --}}
                    <tr>
                        <td align="center" style="padding: 24px;">
                            <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                        </td>
                    </tr>

                    {{-- Title --}}
                    <tr>
                        <td style="padding: 0 32px; font-size:20px; font-weight:bold; color:#333;">
                            {{ $title }}
                        </td>
                    </tr>

                    {{-- Message --}}
                    <tr>
                        <td style="padding: 16px 32px 8px 32px; font-size:15px; color:#444; line-height:1.6;">
                            {{ $message }}
                        </td>
                    </tr>

                    {{-- Optional Action Button --}}
                    @if($url)
                        <tr>
                            <td align="center" style="padding: 32px;">
                                <a href="{{ $url }}" 
                                   style="background:#2F80ED; color:#fff; text-decoration:none; padding:12px 18px; border-radius:6px; font-size:15px; display:inline-block;">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endif

                    {{-- Footer --}}
                    <tr>
                        <td style="padding: 24px 32px; border-top:1px solid #eee;">
                            <p style="font-size:14px; color:#777; margin:0 0 8px;">
                                This email was sent by {{ config('app.name') }}.
                            </p>
                            
                            <p style="font-size:13px; color:#aaa; margin:0;">
                                {{ config('app.name') }} · {{ config('app.url') }}
                            </p>
                        </td>
                    </tr>

                </table>

                {{-- Legal small footer --}}
                <table width="600" cellpadding="0" cellspacing="0" style="margin-top: 16px;">
                    <tr>
                        <td align="center" style="font-size:12px; color:#999;">
                            If you believe this email was sent by mistake, you can ignore it.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
