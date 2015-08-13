/*cookie history*/
var novelKit = {
	_version_:'0.0.1',
	View:function( nid ){
		$.ajax({
			 url: '/index.php?m=Novel&g=Home&a=ts',
             data: {'nid':nid,'act':'view'},
             type: "POST",
             dataType:'json',
             success:function( d){
             	//
             },
             error:function(d){
             	//
             }
        });
	},
	Read:function(nid, ncntid , title ){
		$.ajax({
			 url: '/index.php?m=Novel&g=Home&a=ts',
             data: {'nid':nid,'ncntid':ncntid,'act':'read'},
             type: "POST",
             dataType:'json',
             success:function( d){
             	//
             },
             error:function(d){
             	//
             }
         });
	},
	Recommend:function( nid )
	{
		$.ajax({
			 url: '/index.php?m=Novel&g=Home&a=ts',
             data: {'nid':nid,'act':'recomm'},
             type: "POST",
             dataType:'json',
             success:function( d){
             	//
             },
             error:function(d){
             	//
             }
        });
	},
};
