<script type="text/javascript">
    jQuery(document).ready(function(){
    var modernAccordion = $('.mdn-accordion');
    if( modernAccordion.length > 0 ) {
    modernAccordion.each(function(){
      var each_accordion = $(this);
      $('.accordion-toggle:checked').siblings('ul').attr('style', 'display:none;').stop(true,true).slideDown(300);
      each_accordion.on('change', '.accordion-toggle', function(){
        var toggleAccordion = $(this);
        if (toggleAccordion.is(":radio")) {
          toggleAccordion.closest('.mdn-accordion').find('input[name="' + $(this).attr('name') + '"]').siblings('ul')
          .attr('style', 'display:block;').stop(true,true).slideUp(300); 
          toggleAccordion.siblings('ul').attr('style', 'display:none;').stop(true,true).slideDown(300);                 
         } else {       
          (toggleAccordion.prop('checked')) ? toggleAccordion.siblings('ul')
          .attr('style', 'display:none;').stop(true,true).slideDown(300) : toggleAccordion.siblings('ul')
          .attr('style', 'display:block;').stop(true,true).slideUp(300); 
         }
      });
    });
    }
    $(document).on('click', '.mdn-accordion .accordion-title', function(e) {
    var $mdnRippleElement = $('<span class="mdn-accordion-ripple" />'),
    $mdnButtonElement = $(this),
    mdnBtnOffset = $mdnButtonElement.offset(),
    mdnXPos = e.pageX - mdnBtnOffset.left,
    mdnYPos = e.pageY - mdnBtnOffset.top,
    mdnSize = parseInt(Math.min($mdnButtonElement.height(), $mdnButtonElement.width()) * 0.5),
    mdnAnimateSize = parseInt(Math.max($mdnButtonElement.width(), $mdnButtonElement.height()) * Math.PI);
    $mdnRippleElement
    .css({
      top: mdnYPos,
      left: mdnXPos,
      width: mdnSize,
      height: mdnSize,
      backgroundColor: $mdnButtonElement.data("accordion-ripple-color")
    })
    .appendTo($mdnButtonElement)
    .animate({
      width: mdnAnimateSize,
      height: mdnAnimateSize,
      opacity: 0
    }, 800, function() {
      $(this).remove();
    });
    }); 
    });
</script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
<script type="text/javascript">
    var userId = 24 ;
    var menu = "<ul class='mdn-accordion indigo-accordion-theme'>";
    $( document ).ready(function() {
        var loopData = "";
        $.ajax({
            type:"POST",
            url:'sidebarMenu' ,
            async: false,
            data: {
                userId: userId,
            },
           success:function(res){
            console.log(res);
            var MainMenuTitle = "" ;
            var mainMenuIcon = "";
            for(i=0;i<res.length;i++){
                if(res[i].subMenu == 1){
                  
                    var mainSubMenuData = "" ;
                    MainMenuTitle = res[i].title ;
    
                    if(res[i].icon!=null && res[i].icon.length>0){
                      mainMenuIcon = res[i].icon;
                    }else{
                      mainMenuIcon = "";
                    }
    
                    $.ajax({
                    type:"POST",
                    url:'sidebarSubMenu' ,
                    async: false,
                    data: {
                        parentMenuId: res[i].id,
                    },
                    success:function(result){
                        // console.log(result);
                        loopData = '   <li class="sub-level"><input class="accordion-toggle" type="checkbox" name ="group-2-'+i+'" id="group-2-'+i+'"><label class="accordion-title" for="group-2-'+i+'">'+ mainMenuIcon + MainMenuTitle +' </label><ul>' ;
                         
                        for(j=0;j<result.length;j++){
                            loopData = loopData + '<li><a class="" href="'+ result[j].url +'"> '+ result[j].icon +'&nbsp;'+ result[j].title +'</a></li>' ;
                        }
                        loopData = loopData + '</ul></li>' ; 
                        menu = menu + loopData ;
                    }
                    });
    
                }else{
                     menu = menu + '<li><a href="'+ res[i].url +'">'+ res[i].icon +'&nbsp;'+ res[i].title +'</a></li>' ;
                }
            }
    
             menu = menu + "</ul>";
             $('#sidebarMenu').append(menu);
           }
        });
          
    });
</script>
<div class="navbar-default sidebar" role="navigation">
    <div id="sidebarMenu" class="sidebar-nav navbar-collapse">
    </div>
    <!-- /.sidebar-collapse -->
</div>
<!-- /.navbar-static-side -->