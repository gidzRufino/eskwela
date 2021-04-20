<div id="printOpt" style="width:350px; margin: 10px auto 0;" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="panel panel-green">
        <div class="panel-heading">
            <h4>Select Form 137 Department to print</h4>
        </div>
        <div class="panel-body">
            <button class="btn btn-sm btn-default" onclick="printForm(1)" style="width: 100%">Elementary</button><br/><br/>
            <button class="btn btn-sm btn-primary" onclick="printForm(2)" style="width: 100%">Junior High School</button><br/><br/>
            <?php if (segment_6 >= 12 && segment_6 <= 13): ?>
                <button class="btn btn-sm btn-danger" onclick="printForm(3)" style="width: 100%">Senior High School</button>
            <?php endif; ?>
        </div>
    </div>

</div>
