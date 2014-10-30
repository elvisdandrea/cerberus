<!--
Arquivo Reports.tpl
Arquivo template carregado ao acessar a aba 'Reports' do sidebar
Substituí a 'DIV' Center da home.tbl com o seu conteudo quando acessado

-#RUN
 >FORM
  >>row
   >>>col <div da seleção de funcionarios>
   >>>col <div da seleção de data de inicio>
   >>>col <div da seleção de data dinal>
-#TABLE

-->

<div class="row" id="run">
    <form action="/raptor/home/tbl">

      <div class="row">

<!-- ---------Div da Seleção de funcionarios-------- -->
        <div class="col-md-6">
           <Label>Funcionários: </label>
           <select name="func" class="form-control chosen-select" multiple>
             <option value="this needs sql queries">this needs sql queries</option>
             <option value="Moar sql queries">Moar sql queries</option>
             <option value="Even moar queries">Even moar queries</option>
             <option value="Lots of queries">Lots of queries</option>
           </select>
        </div>

<!-- -------Div da Seleção de data de inicio-------- -->
        <div class="col-md-3">
           <div class="form-group">
             <label>Data de inicio: </label>
             <input name="idt" type="text" id="inicdata" class="form-control datepick" value="2014-07-01">
           </div>
        </div>

<!-- ---------Div da Seleção de data final---------- -->
        <div class="col-md-3">
           <div class="form-group">
             <label>Data Final: </label>
             <input name="fdt" type="text" id="fimdata" class="form-control datepick" value="{date('Y-m-d')}">
           </div>
        </div>

    </div> <!-- fim da div class row -->

<!-- ---------------Botão Submit-------------------- -->
        <input class="btn btn-primary" type="submit" value="Ok"/>
    </form><!-- fim do form -->
</div>

<!-- Div da Tabela, deve aparecer assim que for enviado o form -->

<div class="row tabela" id="table">
   <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque porta vulputate lectus euismod convallis. Pellentesque viverra varius pellentesque. Suspendisse accumsan iaculis eros, vel feugiat lorem porttitor ut. Sed pellentesque vitae ligula ut consectetur. Donec tristique aliquet lobortis. Vivamus auctor tempus venenatis. Nullam lobortis turpis at dolor luctus semper id sed lorem. Aliquam vulputate vestibulum mi, at porta neque egestas vel. Etiam lacus nisi, porttitor id purus eu, accumsan accumsan quam.</p>
</div>

<!-- -------Scripts adicionais para esta aba--------- -->

<script>
    Forms.Actions('#run');
    $(".chosen-select").chosen();
    $(function() {
        $( ".datepick" ).datepicker({ 
            showButtonPanel: true,
            dateFormat: "yy-mm-dd"  });
    });
</script>