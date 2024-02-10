<div class="card card-rounded mb-4">
    <div class="card-body card-rounded">
        <h4 class="card-title  card-title-dash">{{__('Usage Log')}}</h4>
        <p class="card-subtitle card-subtitle-dash mb-2 mb-lg-4">{{__('Monitor your quota and usage statistics')}}</p>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{__("Module")}}</th>
                    <th class="text-center">{{__("Limit")}}</th>
                    <th class="text-center">{{__("Used")}}</th>
                </tr>
                </thead>
                <tbody>

                <?php
                $i=0;
                $pricing_link = route('pricing-plan');
                foreach($modules as $row)
                {
                    $i++;
                    $row_class="";
                    $has_access = in_array($row->id,$user_module_ids);
                    echo "<tr>";
                    echo "<td>".$i."</td>";
                    echo "<td>".__($row->module_name)."</td>";

                    if(!$has_access) // no access
                    {
                        $str="<a class='badge badge-opacity-warning no-radius py-2 text-decoration-none' href='".route('select-package')."'>".__("Upgrade")."</a>";
                        echo "<td colspan='2' class='text-center'>{$str}</td>";
                    }
                    else
                    {
                        if($row->limit_enabled=='0') echo "<td colspan='2' class='text-center'>".__('No Limit Applicable')."</td>";
                        else{
                            $extra_text = $monthly_limit[$row->id]>0 && $row->extra_text!="" ? " / ".__($row->extra_text) : '';
                            $monthly_limit_subscriber = $monthly_limit[$row->id];
                            $monthly_limit_subscriber .= $extra_text;

                            if($row->id==$module_id_team_member){
                                $usage_info[$module_id_team_member]['usage_count'] = $team_count ?? 0;
                            }

                            
                            echo $monthly_limit[$row->id]>0
                                ? "<td class='text-center'>". $monthly_limit[$row->id]."</td>"
                                : "<td class='text-center'><span class='badge badge-opacity-success'>".__('Unlimited')."</span></td>";

                            echo isset($usage_info[$row->id]['usage_count'])
                                ? "<td class='text-center'>".$usage_info[$row->id]['usage_count']."</td>"
                                : "<td class='text-center'>0</td>";

                        }

                    }
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
