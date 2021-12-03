<?php 
	require_once 'header.php'; 
	
	if (!$user) {
		header('Location: /no');
		exit();
	}
?>
<script>
	var SETTINGS = ["confirm","sounds","dongers","hideme"];
	function inlineAlert(x,y){
		$("#inlineAlert").removeClass("alert-success alert-danger alert-warning hidden");
		if(x=="success"){
			$("#inlineAlert").addClass("alert-success").html("<i class='fa fa-check'></i><b> "+y+"</b>");
		}else if(x=="error"){
			$("#inlineAlert").addClass("alert-danger").html("<i class='fa fa-exclamation-triangle'></i> "+y);
		}else if(x=="cross"){
			$("#inlineAlert").addClass("alert-danger").html("<i class='fa fa-times'></i> "+y);
		}else{
			$("#inlineAlert").addClass("alert-warning").html("<b>"+y+" <i class='fa fa-spinner fa-spin'></i></b>");
		}
	}
	function resizeFooter(){
		var f = $('.footer').outerHeight(true);
		var w = $(window).outerHeight(true);
		$('body').css('margin-bottom',f);
	}
	$(window).resize(function(){
		resizeFooter();
	});
	if (!String.prototype.format) {
	  String.prototype.format = function() {
	    var args = arguments;
	    return this.replace(/{(\d+)}/g, function(match, number) { 
	      return typeof args[number] != 'undefined'
	        ? args[number]
	        : match
	      ;
	    });
	  };
	}
	function setCookie(key,value){
		var exp = new Date();
		exp.setTime(exp.getTime()+(365*24*60*60*1000));
		document.cookie = key+"="+value+"; expires="+exp.toUTCString();
	}
	function getCookie(key){
		var patt = new RegExp(key+"=([^;]*)");
		var matches = patt.exec(document.cookie);
		if(matches){
			return matches[1];
		}
		return "";
	}
	function formatNum(x){
		if(Math.abs(x)>=10000){
			return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		}
		return x;
	}
	$(document).ready(function(){
		resizeFooter();
		for(var i=0;i<SETTINGS.length;i++){
			var v = getCookie("settings_"+SETTINGS[i]);
			if(v=="true"){
				$("#settings_"+SETTINGS[i]).prop("checked",true);	
			}else if(v=="false"){
				$("#settings_"+SETTINGS[i]).prop("checked",false);	
			}			
		}
	});
</script>
		<div class="modal fade" id="confirmModal">
		    <div class="modal-dialog">
		        <div class="modal-content">
		            <div class="modal-header">
		                <div class="close" data-dismiss="modal">×</div>
		                <h4 class="modal-title"><b>Confirm</b></h4>
		            </div>
		            <div class="modal-body">                           
		                <label>Tradelink - <a href="https://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url" target="_blank">Find my trade</a></label>
						<input type="text" class="form-control steam-input" id="tradeurl" value="<?=$_COOKIE['tradeurl']?>">
						<div class="checkbox">
					    	<label>
					      		<input type="checkbox" id="remember" checked=""> Remember tradelink
					    	</label>
						</div>
		            </div>
		            <div class="modal-footer">
		            <button class="btn btn-danger" data-dismiss="modal">Close</button>
		            <button class="btn btn-success" id="offerButton" onclick="offer()">Confirm</button>                
		            </div>
		        </div> 
		    </div>
		</div>		
		<div style="display:inline-block">
							<div class="alert alert-info">
  <b><i class="fa fa-exclamation-circle"></i> After confirming the trade please click "Get coins" | Minimum deposit is 0.10 $ - 100 coins.</b>
</div>
				<div id="inlineAlert" class="alert alert-success" style="font-weight:bold"><i class="fa fa-check"></i><b> Loaded 11 available items.</b></div>
				
				<div class="panel panel-default text-left" id="offerPanel" style="display:none">
				  	<div class="panel-heading">
						<h3 class="panel-title"><b>Trade sent <i class="fa fa-download"></i></b></h3>
				  	</div>
  					<div class="panel-body">
						<span id="offerContent" style="line-height:34px"></span>
						<div class="pull-right"><button class="btn btn-success" id="confirmButton" data-tid="0">Get coins</button></div>
					</div>
				</div>


						<h1><b>Inventory : <span id="left_number">0</span> items</b></h1>		
						<div style="margin-bottom:10px">						
							<div style="display:inline-block;float:right">
								<form class="form-inline">
									<select class="form-control" id="orderBy">
										<option value="0">Default</option>
										<option value="1">Price descending</option>
										<option value="2">Acending price</option>
										<option value="3">Name A-Z</option>
									</select>
								</form>
							</div>				
	  						<div style="overflow:hidden;padding-right:2px">
	    						<input type="text" class="form-control" id="filter" placeholder="Search..." style="width:100%">
	   						</div>
   						</div>  																										
						<div id="left" class="slot-group noselect">
							<span class="reals"></span>
							<span class="bricks">
								<div class="placeholder"></div>
								<div class="placeholder"></div>
								<div class="placeholder"></div>
								<div class="placeholder"></div>
								<div class="placeholder"></div>
								<div class="placeholder"></div>
								<div class="placeholder"></div>
								<div class="placeholder"></div>
								<div class="placeholder"></div>
								<div class="placeholder"></div>
								<div class="placeholder"></div>
								<div class="placeholder"></div>
							</span>		
						</div>											
				
						<h1><b>Deposit</b></h1>
						<button class="btn btn-success btn-lg" style="width:100%" onclick="showConfirm()" id="showConfirmButton">Deposit items<div style="font-size:12px"><span id="sum">0</span> coins</div></button>				
						<div id="right" class="slot-group noselect">
							<span class="reals"></span>
							<span class="bricks">
								<div class="placeholder"></div>
								<div class="placeholder"></div>
								<div class="placeholder"></div>
								<div class="placeholder"></div>
								<div class="placeholder"></div>
								<div class="placeholder"></div>
								<div class="placeholder"></div>
								<div class="placeholder"></div>								
							</span>																																	
						</div>						
				</div>
	<!-- BODY END -->
	
	<!-- jQuery -->
	<script src="style/js/jquery.cookie.js"></script>
	<script>
		var SETTINGS = ["confirm","sounds","dongers","hideme"];
		function inlineAlert(x,y){
			$("#inlineAlert").removeClass("alert-success alert-danger alert-warning hidden");
			if(x=="success"){
				$("#inlineAlert").addClass("alert-success").html("<i class='fa fa-check'></i><b> "+y+"</b>");
			}else if(x=="error"){
				$("#inlineAlert").addClass("alert-danger").html("<i class='fa fa-exclamation-triangle'></i> "+y);
			}else if(x=="cross"){
				$("#inlineAlert").addClass("alert-danger").html("<i class='fa fa-times'></i> "+y);
			}else{
				$("#inlineAlert").addClass("alert-warning").html("<b>"+y+" <i class='fa fa-spinner fa-spin'></i></b>");
			}
		}
		if (!String.prototype.format) {
		  String.prototype.format = function() {
			var args = arguments;
			return this.replace(/{(\d+)}/g, function(match, number) { 
			  return typeof args[number] != 'undefined'
				? args[number]
				: match
			  ;
			});
		  };
		}
		function setCookie(key,value){
			var exp = new Date();
			exp.setTime(exp.getTime()+(365*24*60*60*1000));
			document.cookie = key+"="+value+"; expires="+exp.toUTCString();
		}
		function getCookie(key){
			var patt = new RegExp(key+"=([^;]*)");
			var matches = patt.exec(document.cookie);
			if(matches){
				return matches[1];
			}
			return "";
		}
		function formatNum(x){
			if(Math.abs(x)>=10000){
				return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			}
			return x;
		}
	</script>
	<style>
		.reject{
			opacity:0.5;
		}
		.reject .price{
			background-color: #d21 !important;
			left: 0px !important;
		}
	</style>		
	<script type="text/javascript">
		var DEPOSIT = true;			
	</script>
	<script type="text/javascript" src="style/js/offers.js?v=<?=time()?>"></script>	
<?php require_once 'footer.php'; ?>