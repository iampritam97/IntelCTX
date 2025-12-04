<?php
// partials/tw_components.php
function tw_card_open($extra = '') {
    return "<div class=\"bg-white dark:bg-gray-900 border border-border rounded-xl p-4 shadow-sm $extra\">";
}
function tw_card_close() {
    return "</div>";
}
function tw_metric_card($title, $value, $hint = '', $extra_class = '') {
    $html = "<div class=\"bg-white dark:bg-gray-900 border border-border rounded-xl p-4 shadow-sm $extra_class\">";
    $html .= "<p class=\"text-xs text-slate-500\">".htmlspecialchars($title)."</p>";
    $html .= "<p class=\"text-2xl font-bold text-text0 mt-2\">".htmlspecialchars($value)."</p>";
    if ($hint) $html .= "<p class=\"text-xs text-slate-400 mt-1\">".htmlspecialchars($hint)."</p>";
    $html .= "</div>";
    return $html;
}
function tw_badge($text, $type = 'neutral') {
    $map = [
      'neutral' => 'bg-slate-100 text-slate-800',
      'accent' => 'bg-accent text-white',
      'good' => 'bg-green-600 text-white',
      'warn' => 'bg-yellow-500 text-black',
      'danger' => 'bg-red-600 text-white'
    ];
    $c = $map[$type] ?? $map['neutral'];
    return "<span class=\"text-[11px] px-2 py-0.5 rounded $c\">".htmlspecialchars($text)."</span>";
}
