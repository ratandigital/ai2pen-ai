<div class="col-12 col-lg-3 order-1 order-lg-2">
    <div class="list-group">
        <a href="{{ route('affiliate-dashboard') }}" class="list-group-item list-group-item-action {{ $get_selected_sidebar == 'affiliate-dashboard' ? 'active' : '' }}">{{ __('Dashboard') }}</a>
        <a href="{{ route('affiliate-user-self-settings') }}" class="list-group-item list-group-item-action {{ $get_selected_sidebar == 'affiliate-user-self-settings' ? 'active' : '' }}">{{ __('Settings') }}</a>
        <a href="{{ route('affiliate-withdrawal-methods') }}" class="list-group-item list-group-item-action {{ $get_selected_sidebar == 'affiliate-withdrawal-methods' ? 'active' : '' }}">{{ __('Withdrawal Methods') }}</a>
        <a href="{{ route('affiliate-withdrawal-requests') }}" class="list-group-item list-group-item-action {{ $get_selected_sidebar == 'affiliate-withdrawal-requests' ? 'active' : '' }}">{{ __('Withdrawal Requests') }}</a>
    </div>
    <br><br>
</div>