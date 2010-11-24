<?php include 'imgpath.php'; ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title></title>
		<link rel="stylesheet" type="text/css" href="css/reset.css" /> 
		<link rel="stylesheet" type="text/css" href="css/jquery.treeview.css" /> 
		<link rel="stylesheet" type="text/css" href="css/style.css" /> 
		<link rel="stylesheet" type="text/css" href="css/jquery.lightbox-0.5.css" /> 
		
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.4.min.js"></script>
		<script type="text/javascript" src="js/jquery.treeview.js"></script>
		<script type="text/javascript" src="js/jquery.lightbox-0.5.min.js"></script>
		<script type="text/javascript">
		  var toImport = [];

		  $(document).ready(function() {

        $('#selectAll').click(function() {
		    $('#preview ul input').each(function() {
		      $(this).click();
		    })
		  })

		  
		    $('#btnContainer a').click(function() {
		    //console.log($(this).html());
		    if ($(this).html() == 'Show List') {
   		    $('#btnContainer ul').show();
  		    $(this).html('Hide List');
  		  }
  		  else {
  		    $(this).html('Show List');
  		    $('#btnContainer ul').hide();
  		  }
 		    return false;
		  });

		    
		    $('#btnContainer li').live('click', function() {
		      $($('form input')[0]).val($(this).html());
		      $('form').submit();
		    });
		    
		    $("#tree").treeview();
		    $('form').submit(function() {
		      $('.loading').show();
		      $('span').removeClass('highlight_active');
		      $.ajax({
		        url: 'search.php?search='+$('#searchBox').val(),
		        dataType: 'json',
		        success: function(data) {
              renderData(data);              
		        }
		      });
		      return false;
		    });
		    
		    $('span.folder').click(function() {
		      $('span').removeClass('highlight_active');
		      var path = [$(this).html()];
		      var el = this;
		      
		      $(this).addClass('highlight_active');
		      
		      while(!$(el).hasClass('filetree')){
		        el = $(el).parent();
		        if (el.tagName != 'ul') {
		          el = $(el).parent();
		        }
		        if ($(el).parent().children('span').html() != null) {
  		        path.push($(el).parent().children('span').html());
  		      }
		        
		      }
		      path.reverse();
		      path = path.join('/');
		      path = '/'+path+'/';
		      $('.loading').show();
		      $.ajax({
		        url: 'getFolderContents.php?folder='+path,
		        dataType: 'json',
		        success: function(data) {
              renderData(data, path);              
		        }
		      });
		    });
		    $('button').click(function(){ alert('Images have been imported into ECL.'); });
		  });
		  
		  
		  
		  
		  function renderData(data, path) {
        $('#preview ul').html('');
        if (data.length > 0) {

		    }
		    else {
		      if (path) {
  		      $('#preview ul').append('<li>No image files in folder '+path+'</li>');
  		    }
  		    else {
  		      $('#preview ul').append('<li>No image files found</li>');
  		    }
		    }
        var i = 0;
        while (i<data.length) {
          var renderClass = '';
          var checked = '';
          if (jQuery.inArray('<?php echo IMGSDIRPATH; ?>'+path+''+data[i], toImport) != -1) {
            renderClass='check_selected';
            checked = 'checked="true"';
          }
          if (jQuery.inArray(data[i], toImport) != -1) {
            renderClass='check_selected';
            checked = 'checked="checked"';
          }
          if (path) {
          $('#preview ul').append('<li class="'+renderClass+'"><input type="checkbox" value="<?php echo IMGSDIRPATH; ?>'+path+''+data[i]+'" '+checked+'><a title="<?php echo IMGSDIRPATH; ?>'+path+''+data[i]+'" href="<?php echo IMGSDIRPATH; ?>'+path+''+data[i]+'"><img src="<?php echo IMGSDIRPATH; ?>'+path+'/'+data[i].replace('web.jpg', 'thumb.jpg')+'"></a><span>'+data[i]+'</span></li>');
          }
          else {
            var nameData = data[i].split('/');
            $('#preview ul').append('<li class="'+renderClass+'"><input type="checkbox" value="'+data[i]+'" '+checked+'><a title="'+data[i]+'" href="'+data[i]+'"><img src="'+data[i].replace('web.jpg', 'thumb.jpg')+'"></a><span>'+nameData[nameData.length-1]+'</span></li>');
          }          
          i++;
        }
        
        $(function() {
  	     $('#preview li a').lightBox({fixedNavigation:true});
        });
        
        $('#preview li input').click(function() {
          
          
          $(this).parent().toggleClass('check_selected');
          
          var imgVal = $(this).val();
          //add to the toImport array if they checked it
          if ($(this).parent().hasClass('check_selected')) {
            if (jQuery.inArray(imgVal, toImport) == -1) {
              toImport.push(imgVal);
            }
          }
          else {
            var i = 0;
            $(toImport).each(function() {
              if (this == imgVal) {
                toImport.remove(i);
              }
              i++;
            });
          }
          $('#btnContainer span').html(toImport.length);
          var importList = '';
          $(toImport).each(function() {
  		      importList += '<li>'+this+'</li>';
  		    });
  		    $('#btnContainer ul').html(importList);
        })
        
              
        $('.loading').hide();

		  
		  }
		
		  		  
      //jresig's array.remove
      if (!Array.prototype.remove){
         Array.prototype.remove = function(from, to){
             this.splice(from,
                 !to ||
                 1 + to - from + (!(to < 0 ^ from >= 0) && (to < 0 || -1) * this.length));
             return this.length;
         };
      }

		</script>
	</head>
	<body>
    <div id="nav">
	
	
	<?php
include 'dirlist.php';
$dirList = directory_list(IMGSDIRPATH, false, true, '', true);


function renderDirList($dirList) {
  foreach ($dirList as $k=>$folder) {
    if (count($folder) > 2) {
        $fpath .= $k . '/';
        echo '<li class="closed"><span class="folder">'.$k.'</span>';
        echo '<ul>';
        renderDirList($folder);
        echo '</ul>';
        echo '</li>';

    }
    else {
      if ($folder != '.' && $folder != '..') {
        $fpath .= $k . '/';
        echo '<li class="closed"><span class="folder">'.$k.'</span></li>';
      }
    }
    
  }
}

echo '<ul id="tree" class="filetree">';
renderDirList($dirList);
echo '</ul>';


?>

</div>
<div class="loading"><img src="images/ajax-loader.gif"></div>	
  <input type="checkbox" id="selectAll">Select All
<div id="preview">
  <form>
    Search: <input type="text" value="" id="searchBox"><input type="submit" value="submit">
  </form>
  <ul>
  
  </ul>
</div>
  <div id="btnContainer">
    <p>Total to Migrate: <span>0</span> images <br /><br /><a href="#">Show List</a></p>
    <button>Migrate to CMS</button><br />
    <ul style="display: none"></ul>
  </div>
	</body>
</html>
