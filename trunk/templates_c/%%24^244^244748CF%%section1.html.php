<?php /* Smarty version 2.6.13, created on 2006-05-11 11:59:24
         compiled from section1.html */ ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<meta http-equiv="Content-Language" content="zh-CN">
<meta name="Author" content="Dengwei">
<title>title</title>
</head>
<body>
<table style="width: 300px;height:300px;border:1px solid Red">
<?php unset($this->_sections['list2']);
$this->_sections['list2']['name'] = 'list2';
$this->_sections['list2']['loop'] = is_array($_loop=$this->_tpl_vars['list2']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['list2']['show'] = true;
$this->_sections['list2']['max'] = $this->_sections['list2']['loop'];
$this->_sections['list2']['step'] = 1;
$this->_sections['list2']['start'] = $this->_sections['list2']['step'] > 0 ? 0 : $this->_sections['list2']['loop']-1;
if ($this->_sections['list2']['show']) {
    $this->_sections['list2']['total'] = $this->_sections['list2']['loop'];
    if ($this->_sections['list2']['total'] == 0)
        $this->_sections['list2']['show'] = false;
} else
    $this->_sections['list2']['total'] = 0;
if ($this->_sections['list2']['show']):

            for ($this->_sections['list2']['index'] = $this->_sections['list2']['start'], $this->_sections['list2']['iteration'] = 1;
                 $this->_sections['list2']['iteration'] <= $this->_sections['list2']['total'];
                 $this->_sections['list2']['index'] += $this->_sections['list2']['step'], $this->_sections['list2']['iteration']++):
$this->_sections['list2']['rownum'] = $this->_sections['list2']['iteration'];
$this->_sections['list2']['index_prev'] = $this->_sections['list2']['index'] - $this->_sections['list2']['step'];
$this->_sections['list2']['index_next'] = $this->_sections['list2']['index'] + $this->_sections['list2']['step'];
$this->_sections['list2']['first']      = ($this->_sections['list2']['iteration'] == 1);
$this->_sections['list2']['last']       = ($this->_sections['list2']['iteration'] == $this->_sections['list2']['total']);
?>
  <tr>
  <?php unset($this->_sections['list1']);
$this->_sections['list1']['name'] = 'list1';
$this->_sections['list1']['loop'] = is_array($_loop=$this->_tpl_vars['list2'][$this->_sections['list2']['index']]) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['list1']['show'] = true;
$this->_sections['list1']['max'] = $this->_sections['list1']['loop'];
$this->_sections['list1']['step'] = 1;
$this->_sections['list1']['start'] = $this->_sections['list1']['step'] > 0 ? 0 : $this->_sections['list1']['loop']-1;
if ($this->_sections['list1']['show']) {
    $this->_sections['list1']['total'] = $this->_sections['list1']['loop'];
    if ($this->_sections['list1']['total'] == 0)
        $this->_sections['list1']['show'] = false;
} else
    $this->_sections['list1']['total'] = 0;
if ($this->_sections['list1']['show']):

            for ($this->_sections['list1']['index'] = $this->_sections['list1']['start'], $this->_sections['list1']['iteration'] = 1;
                 $this->_sections['list1']['iteration'] <= $this->_sections['list1']['total'];
                 $this->_sections['list1']['index'] += $this->_sections['list1']['step'], $this->_sections['list1']['iteration']++):
$this->_sections['list1']['rownum'] = $this->_sections['list1']['iteration'];
$this->_sections['list1']['index_prev'] = $this->_sections['list1']['index'] - $this->_sections['list1']['step'];
$this->_sections['list1']['index_next'] = $this->_sections['list1']['index'] + $this->_sections['list1']['step'];
$this->_sections['list1']['first']      = ($this->_sections['list1']['iteration'] == 1);
$this->_sections['list1']['last']       = ($this->_sections['list1']['iteration'] == $this->_sections['list1']['total']);
?>
    <td><?php echo $this->_tpl_vars['list2'][$this->_sections['list2']['index']][$this->_sections['list1']['index']]['title']; ?>
</td>
  <?php endfor; endif; ?>
  </tr>
<?php endfor; endif; ?>
</table>
</body>
</html>