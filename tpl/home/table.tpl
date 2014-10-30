<table class="table table-striped table-bordered">
 <thead>
  <tr>
    <th>Jill</th>
    <th>Smith</th>
    <th>50</th>
  </tr>
 </thead>
 <tbody>
  {foreach from=$res item="row"}
    <tr>
      <td>{$row}</td>
      <td>{$idt}</td>
      <td>{$fdt}</td>
    </tr>
  {/foreach}
 </tbody>
</table>