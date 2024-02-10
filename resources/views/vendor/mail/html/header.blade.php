<tr>
<td class="header">
{{-- Email template, so its need inline styling --}}
<h1 style="text-align:center;margin-top: 30px;">
	@if (trim($slot) === 'Laravel')
	<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
	@else
	{{ $slot }}
	@endif
</h1>
<!-- </a> -->
</td>
</tr>
