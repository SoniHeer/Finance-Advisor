<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>You're Invited to Join {{ $family->name }}</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
@media only screen and (max-width:600px) {
    .container { width:100% !important; }
    .content { padding:24px !important; }
}
</style>

</head>

<body style="margin:0;padding:0;background:#f4f7fb;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;color:#0f172a;">

<!-- PREHEADER (Hidden Preview Text) -->
<div style="display:none;max-height:0;overflow:hidden;">
You’ve been invited to join {{ $family->name }} on FinanceAI.
</div>

<table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 16px;">
<tr>
<td align="center">

<table class="container" width="520" cellpadding="0" cellspacing="0"
style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 30px 70px rgba(15,23,42,.12);">

<!-- HEADER -->
<tr>
<td style="background:linear-gradient(135deg,#2563eb,#06b6d4);padding:32px;text-align:center;color:#ffffff;">
<h1 style="margin:0;font-size:22px;font-weight:800;">
FinanceAI
</h1>
<p style="margin:8px 0 0;font-size:13px;opacity:.9;">
Secure Family Financial Intelligence
</p>
</td>
</tr>

<!-- CONTENT -->
<tr>
<td class="content" style="padding:40px 36px;">

<h2 style="margin:0 0 18px;font-size:18px;font-weight:700;">
You’re Invited 👋
</h2>

<p style="margin:0 0 18px;font-size:14px;line-height:1.7;color:#334155;">
<strong>{{ $inviterName ?? 'A family member' }}</strong>
has invited you to join the shared finance workspace:
</p>

<div style="margin:20px 0 28px;padding:16px;background:#f1f5f9;border-radius:12px;text-align:center;font-weight:700;font-size:15px;">
{{ $family->name }}
</div>

<p style="margin:0 0 28px;font-size:14px;line-height:1.7;color:#334155;">
Join to collaborate on income tracking, expense management, and real-time financial insights powered by AI.
</p>

<!-- CTA -->
<div style="text-align:center;margin-bottom:28px;">
<a href="{{ $acceptUrl }}"
style="display:inline-block;padding:14px 28px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:10px;font-weight:700;font-size:14px;">
Accept Invitation →
</a>
</div>

<!-- FALLBACK LINK -->
<p style="font-size:12px;color:#64748b;line-height:1.6;">
If the button doesn’t work, copy and paste this link into your browser:
</p>

<p style="word-break:break-all;font-size:11px;color:#2563eb;">
{{ $acceptUrl }}
</p>

<!-- EXPIRY -->
<p style="margin-top:24px;font-size:12px;color:#64748b;">
⏳ This invitation expires on
<strong>{{ optional($invite->expires_at)->format('d M Y, h:i A') }}</strong>.
</p>

<!-- SECURITY -->
<p style="margin-top:16px;font-size:12px;color:#94a3b8;">
If you were not expecting this invitation, you can safely ignore this email.
No account will be created without your action.
</p>

</td>
</tr>

<!-- FOOTER -->
<tr>
<td style="background:#f8fafc;padding:20px;text-align:center;font-size:11px;color:#64748b;">
© {{ date('Y') }} FinanceAI · Secure Multi-Tenant Budgeting Platform
</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>
