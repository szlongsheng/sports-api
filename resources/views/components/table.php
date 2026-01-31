<?php
$columns = $columns ?? [];
$data = $data ?? [];
$idField = $idField ?? 'id';
$actions = $actions ?? [];
$selectable = $selectable ?? true;
?>
<div class="table-wrap">
  <table class="table">
    <thead>
      <tr>
        <?php if ($selectable): ?>
        <th class="w-10">
          <input type="checkbox" class="rounded border-slate-600 bg-slate-800 text-emerald-500 focus:ring-emerald-500"
            x-model="selectAll" @change="toggleSelectAll()">
        </th>
        <?php endif; ?>
        <?php foreach ($columns as $col): ?>
        <th class="<?= $col['class'] ?? '' ?>"><?= htmlspecialchars($col['label']) ?></th>
        <?php endforeach; ?>
        <?php if (!empty($actions)): ?>
        <th class="w-32 text-right">操作</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($data as $row): ?>
      <tr>
        <?php if ($selectable): ?>
        <td>
          <input type="checkbox" class="rounded border-slate-600 bg-slate-800 text-emerald-500 focus:ring-emerald-500 row-check"
            value="<?= htmlspecialchars($row[$idField] ?? '') ?>" @change="toggleRow()">
        </td>
        <?php endif; ?>
        <?php foreach ($columns as $col): ?>
        <td class="<?= $col['class'] ?? '' ?>">
          <?php
          $key = $col['key'] ?? $col['label'];
          $val = $row[$key] ?? '';
          if (!empty($col['render']) && is_callable($col['render'])):
            echo $col['render']($row);
          else:
            echo htmlspecialchars($val);
          endif;
          ?>
        </td>
        <?php endforeach; ?>
        <?php if (!empty($actions)): ?>
        <td class="text-right space-x-2">
          <?php foreach ($actions as $act): ?>
          <button type="button" class="text-emerald-400 hover:text-emerald-300 text-sm"
            onclick="<?= htmlspecialchars($act['onclick'] ?? '') ?>"><?= htmlspecialchars($act['label']) ?></button>
          <?php endforeach; ?>
        </td>
        <?php endif; ?>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
