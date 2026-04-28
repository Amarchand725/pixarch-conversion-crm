<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Account Credentials</title>
</head>

<body style="margin:0; padding:0; background:#f1f3f4; font-family:Arial, sans-serif;">

    <!-- Outer Wrapper -->
    <table width="100%" style="background:#f1f3f4; padding:40px 0;">
        <tr>
            <td align="center">

                <!-- Card -->
                <table width="600" style="background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.08);">

                    <!-- Logo Section -->
                    <tr>
                        <td align="center" style="padding:30px 20px; background:#ffffff;">
                            <div style="text-align:center; margin-bottom:10px;">
            
                                <!-- Logo (use direct URL) -->
                                <img src="{{ config('app.APP_URL') }}/back-office/assets/img/branding/logo-og.png"
                                    alt="Company Logo"
                                    style="height:50px; margin-bottom:10px;">

                                <!-- App Name -->
                                <div style="font-size:18px; font-weight:bold; color:#111827;">
                                    {{ config('app.name', '100KEYS UAE') }}
                                </div>

                            </div>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="height:1px; background:#e5e7eb;"></td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:30px; color:#111827;">

                            <h2 style="margin:0 0 10px;">Welcome, {{ $user->name }}</h2>

                            <p style="color:#4b5563; font-size:14px; line-height:1.6;">
                                Your account has been created successfully. You can use the credentials below to log in.
                            </p>

                            <!-- Credentials Card -->
                            <div style="margin-top:20px; background:#f9fafb; padding:20px; border-radius:10px;">

                                <p style="margin:0 0 10px; font-size:14px;">
                                    <strong>Email:</strong> {{ $user->email }}
                                </p>

                                <p style="margin:0; font-size:14px;">
                                    <strong>Password:</strong> {{ $plainPassword }}
                                </p>

                            </div>

                            <!-- Warning -->
                            <p style="margin-top:20px; color:#b91c1c; font-size:13px;">
                                ⚠️ Please change your password after first login for security.
                            </p>

                            <!-- Button -->
                            <div style="text-align:center; margin:30px 0;">
                                <a href="{{ url('/login') }}"
                                   style="background:#1a73e8; color:#ffffff; text-decoration:none; padding:12px 24px; border-radius:8px; font-size:14px; display:inline-block;">
                                    Sign in to your account
                                </a>
                            </div>

                            <p style="font-size:13px; color:#6b7280; line-height:1.5;">
                                If you did not expect this email, you can safely ignore it.
                            </p>

                            <p style="margin-top:25px; font-size:13px; color:#111827;">
                                Thanks,<br>
                                <strong>{{ config('app.name', '100KEYS UAE') }}</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:15px; text-align:center; font-size:12px; color:#9ca3af; background:#f9fafb;">
                            © {{ date('Y') }} {{ config('app.name', '100KEYS UAE') }}. All rights reserved.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>