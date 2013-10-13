<script language = "JavaScript">
$(document).ready(function()
  {
    $('#id_Number_Project').change(select_number_project);
    $('#id_name_mark').change(select_work);

  }
);


function select_number_project(eventObj)
{
  $.post('select_project.php',
    {'data': $(eventObj.target).val()},
    success1,
    'html');
  $.post('get_gip.php',
    {'data': $(eventObj.target).val()},
    success2,
    'html');
 
  function success1(returnData)
  {
    $('#id_Name_Project').html(returnData);
  }
  function success2(returnData)
  {
    $('#id_GIP').html(returnData);
  }
};


function select_work(eventObj)
{
  $.post('select_work.php',
    {'data': $(eventObj.target).val()},
    success,
    'html');


  function success(returnData)
  {
    $('#id_work').html(returnData);

    $.post('format.php',
      {'id_work': $('#id_name_work').val()},
      function(returnData) {$('#id_format').html(returnData);},
      'html');
  }

};
</script>

<script language = "JavaScript" >
//обработка выбора списка Наименование работ
function format(obj) {
  $.post('format.php',
    {'id_work': $('#id_name_work').val()},
    function(returnData) {$('#id_format').html(returnData);},
    'html');
}

</script>

<script type = "text/javascript">
$(document).ready(function()
  {
    $('.f-nav.f-nav-tabs>li>a').click(activateLI);
  }
);

function activateLI(eventObj)
{

  if ($(eventObj.target).parent().hasClass('active'))
  {
    return true;
  } else
  {
    $(eventObj.target).parent().siblings().removeClass('active');
    $(eventObj.target).parent().addClass('active');
    return true;
  }


}
</script>

<script>
$(document).ready(function()
  {

    $('#object1').click(function()
      {
        $('.report1').css('display', 'block');
      }
    );

    $('#object1').dblclick(function()
      {
        $('.report1').css('display', 'none');
      }
    );

    //====================================
    $('#object2').click(function()
      {
        $('.report2').css('display', 'block');
      }
    );
    $('#object2').dblclick(function()
      {
        $('.report2').css('display', 'none');
      }
    );
    //====================================

    $('#object3').click(function()
      {
        $('.report3').css('display', 'block');
      }
    );
    $('#object3').dblclick(function()
      {
        $('.report3').css('display', 'none');
      }
    );
    //====================================

    $('#object4').click(function()
      {
        $('.report4').css('display', 'block');
      }
    );
    $('#object4').dblclick(function()
      {
        $('.report4').css('display', 'none');
      }
    );
    //====================================

    $('#object5').click(function()
      {
        $('.report5').css('display', 'block');
      }
    );
    $('#object5').dblclick(function()
      {
        $('.report5').css('display', 'none');
      }
    );
    //====================================

    $('#object6').click(function()
      {
        $('.report6').css('display', 'block');
      }
    );
    $('#object6').dblclick(function()
      {
        $('.report6').css('display', 'none');
      }
    );
    //====================================

    $('#object7').click(function()
      {
        $('.report7').css('display', 'block');
      }
    );
    $('#object7').dblclick(function()
      {
        $('.report7').css('display', 'none');
      }
    );
    //====================================

    $('#object8').click(function()
      {
        $('.report8').css('display', 'block');
      }
    );
    $('#object8').dblclick(function()
      {
        $('.report8').css('display', 'none');
      }
    );
    //====================================
    $('#object9').click(function()
      {
        $('.report9').css('display', 'block');
      }
    );
    $('#object9').dblclick(function()
      {
        $('.report9').css('display', 'none');
      }
    );
    //====================================
    $('#object10').click(function()
      {
        $('.report10').css('display', 'block');
      }
    );
    $('#object10').dblclick(function()
      {
        $('.report10').css('display', 'none');
      }
    );
    //====================================

    $('#object11').click(function()
      {
        $('.report11').css('display', 'block');
      }
    );
    $('#object11').dblclick(function()
      {
        $('.report11').css('display', 'none');
      }
    );
    //====================================

    $('#admin_tabel').click(function()
      {
        $('.report8').css('display', 'block');
      }
    );
    $('#admin_tabel').dblclick(function()
      {
        $('.report8').css('display', 'none');
      }
    );	
	

    //====================================

    $('#object_performance').click(function()
      {
        $('.report_performance').css('display', 'block');
      }
    );
    $('#object_performance').dblclick(function()
      {
        $('.report_performance').css('display', 'none');
      }
    );
    //====================================
    $('#norma').click(function()
      {
        $('.report_norma').css('display', 'block');
      }
    );
    $('#norma').dblclick(function()
      {
        $('.report_norma').css('display', 'none');
      }
    );
    //====================================


  }
);


</script>

