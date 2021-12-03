</div>
	
<!-- FOOTER CONTENT -->
<div class="bottommenu">
	<div class="footer">
		<div class="copyright">
		  BetWing - eSports Betting &copy; Copyright 2017
		</div>
		<div class="atisda">
			Powered by Steam &middot; Developed by <a href="http://jrdev.ga" target="_blank">j0ta </a> 
		</div>
	</div>
</div>

<script type="text/javascript" src="source/bootstrap-3.3.6-dist/js/jquery.js"></script>
<script type="text/javascript" src="source/bootstrap-3.3.6-dist/js/bootstrap.js"></script>
<script type="text/javascript" src="source/js/countdown.js"></script>
<script type="text/javascript" src="source/js/sslider.js"></script>
<script type="text/javascript" src="source/js/clock.js"></script>
<script>
	var d = new Date(<?php echo $today['year'].",".$today['mon'].",".$today['mday'].",".$today['hours'].",".$today['minutes'].",".$today['seconds']; ?>);
	startTime(d);
</script>
</body>
</html>