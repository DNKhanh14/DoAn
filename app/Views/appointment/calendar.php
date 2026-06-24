<div class="bk-cal">
  <div class="bk-cdays">
    <?php foreach($days as $day): ?>
    <div class="bk-cdcol">
      <span class="bk-cdnm"><?=htmlspecialchars($day['day_label'])?></span>
      <span class="bk-cddt"><?=htmlspecialchars($day['date_label'])?></span>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="bk-cslots">
    <?php foreach($days as $day): ?>
    <div class="bk-cscol">
      <?php if(empty($day['slots'])): ?>
        <span class="bk-cem">—</span>
      <?php else: ?>
        <?php foreach($day['slots'] as $slot): ?>
        <input type="radio" id="<?=htmlspecialchars($slot['id'])?>" name="desired_date_time" value="<?=htmlspecialchars($slot['value'])?>">
        <label class="bk-ts" for="<?=htmlspecialchars($slot['id'])?>"><?=htmlspecialchars($slot['label'])?></label>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
</div>
