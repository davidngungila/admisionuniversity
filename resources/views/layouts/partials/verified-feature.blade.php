@php
  $verifyUrl = url('/verify-admission?application_number='.($application->application_number ?? ''));
  $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=90x90&data='.urlencode($verifyUrl);
@endphp
<div style="position:relative; margin-top:14px; padding:14px 16px; background:var(--acacia-100, #E1EFEA); border:1px solid #c8d7a8; border-radius:12px; text-align:center;">
  <div style="position:absolute;top:8px;right:12px;transform:rotate(7deg);border:2px solid #2E7D6B;color:#2E7D6B;font-weight:800;letter-spacing:.12em;font-size:10px;padding:4px 10px;border-radius:8px;opacity:.9;">VERIFIED</div>
  <div style="display:inline-flex;align-items:center;gap:8px;background:#fff;color:#2E7D6B;border:1px solid #c8d7a8;padding:6px 14px;border-radius:24px;font-weight:800;font-size:11px;letter-spacing:.06em;text-transform:uppercase;">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path d="M20 6L9 17l-5-5"/></svg> Record Verified Authentic
  </div>
  <div style="margin-top:8px; font-size:11px; color:#6B5A48; line-height:1.4;">
    Application <strong class="mono" style="color:#07364F;">{{ $application->application_number ?? '—' }}</strong> &middot; Status: <strong>{{ $application->status }}</strong>
  </div>
  <div style="margin-top:10px; display:flex; align-items:center; justify-content:center; gap:12px; flex-wrap:wrap;">
    <img src="{{ $qrUrl }}" alt="QR Verify" style="width:72px;height:72px; border:1px solid #E4D7C2; border-radius:8px; background:#fff; padding:4px;">
    <div style="text-align:left; font-size:10px; color:#6B5A48; line-height:1.4;">
      <div style="font-weight:700; color:#07364F;">Verify Online</div>
      <div style="word-break:break-all; max-width:260px;">{{ $verifyUrl }}</div>
      <div style="margin-top:4px;"><span style="background:#07364F;color:#fff;padding:2px 6px;border-radius:6px;font-size:9px;font-weight:700;">SCAN QR</span> or visit the link to verify</div>
    </div>
  </div>
</div>