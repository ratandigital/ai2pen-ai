<script src="{{ asset('assets/js/pages/member/payment.transaction-log.js') }}"></script>
@if($is_admin)
    <script>
        "use strict";
        var comparison_chart_labels = <?php echo json_encode(array_values($month_names))?>;
        var comparison_chart_data1= <?php echo json_encode(array_values($this_year_earning))?>;
        var comparison_chart_data2= <?php echo json_encode(array_values($last_year_earning))?>;
        var comparison_chart_year = "{{$year}}";
        var comparison_chart_lastyear = "{{$lastyear}}";
        var comparison_chart_steps = "{{$steps}}";
        var user_summary_data = <?php echo json_encode($user_summary_data)?>;
        var user_summary_label = <?php echo json_encode($user_summary_label)?>;
        var user_step_size = "{{$user_step_size}}";
        var user_locale = "{{__('User')}}";
    </script>
    <script src="{{ asset('assets/js/pages/member/payment.transaction-log-summary.js') }}"></script>
@endif