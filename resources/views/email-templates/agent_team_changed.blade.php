<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Recovery Team Assignment Update</title>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; margin:0; padding:0; background:#f6f6f6; }
        .container { width:100%; max-width:680px; margin:24px auto; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.06); }
        .header { padding:20px; background:#0b5ed7; color:#fff; }
        .content { padding:24px; color:#333; line-height:1.5; }
        .footer { padding:16px 24px; font-size:13px; color:#666; background:#f4f4f4; }
        .muted { color:#6b7280; font-size:13px; }
        .meta { margin:12px 0; padding:12px; background:#fafafa; border:1px solid #eee; border-radius:6px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin:0; font-size:18px;">GreenDriveConnect — Team Update</h2>
        </div>

        <div class="content">
            @php
                $fullName = trim($agent->first_name . ' ' . ($agent->last_name ?? ''));
                $applicationId = $agent->reg_application_id ?? 'N/A';
            @endphp

            <p style="margin-top:0">Hello <strong>{{ $fullName }}</strong>,</p>

            {{-- 📌 ASSIGNED MESSAGE --}}
            @if($action === 'assigned')
                <p>
                    We are happy to inform you that you have been <strong>assigned to the Recovery Team</strong>.
                </p>

                <p>
                    Please review the details below:
                </p>

                <div class="meta">
                    <div><strong>Status:</strong> Assigned to Recovery Team</div>
                    <div><strong>Application ID:</strong> {{ $applicationId }}</div>
                    <div><strong>City:</strong> {{ $agent->current_city->city_name ?? 'N/A' }}</div>
                    <div><strong>Zone:</strong> {{ $agent->zone->name ?? 'N/A' }}</div>
                </div>

                <p>
                    Kindly coordinate with your team leader for further instructions.
                </p>

            {{-- 📌 REMOVED MESSAGE --}}
            @else
                <p>
                    This is to inform you that you have been <strong>removed from the Recovery Team</strong>.
                </p>

                <p>
                    Your updated details are as follows:
                </p>

                <div class="meta">
                    <div><strong>Status:</strong> Removed from Recovery Team</div>
                    <div><strong>Application ID:</strong> {{ $applicationId }}</div>
                    <div><strong>City:</strong> {{ $agent->current_city->city_name ?? 'N/A' }}</div>
                    <div><strong>Zone:</strong> {{ $agent->zone->name ?? 'N/A' }}</div>
                </div>

                <p>
                    For any questions or clarifications, kindly reach out to the HR team.
                </p>

            @endif

            <hr style="margin:20px 0; border:none; border-top:1px solid #eef2f6;" />

            <p class="muted">
                This is an automated update regarding your team assignment status.
            </p>
        </div>

        <div class="footer">
            @php
                $footerLines = explode("\n", $footerContent);
            @endphp

            @foreach ($footerLines as $line)
                <div>{!! nl2br(e($line)) !!}</div>
            @endforeach

            <div style="margin-top:8px; font-size:12px; color:#9aa0a6;">
                &copy; {{ date('Y') }} GreenDriveConnect. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
