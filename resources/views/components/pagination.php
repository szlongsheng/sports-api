<?php
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
$total = $total ?? 0;
?>
<div class="flex items-center justify-between mt-4" x-data="pagination(<?= $page ?>, <?= $totalPages ?>, <?= $total ?>)">
  <div class="text-sm text-slate-500">
    共 <span x-text="total"></span> 条
  </div>
  <div class="flex items-center gap-1">
    <button type="button" class="btn-ghost py-1.5 px-2 text-sm" :disabled="page <= 1" @click="prev()">上一页</button>
    <span class="px-3 py-1 text-sm text-slate-400">
      <span x-text="page"></span> / <span x-text="totalPages"></span>
    </span>
    <button type="button" class="btn-ghost py-1.5 px-2 text-sm" :disabled="page >= totalPages" @click="next()">下一页</button>
  </div>
</div>
