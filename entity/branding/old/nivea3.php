<?php

class nivea3 extends BrandingItem {

	protected $_startDate = '2/22/2012';
	protected $_endDate = '2/23/2012';

	protected function _isSuitable() {
		global $page, $nRootPage;
		
		return true;
	}

	public function display($class) {
		$rand = mt_rand(1, 100);
		if($rand <= 20)
		{
			echo "
				<body id=\"body\" class=\"".$class."\" style=\"background: url('/img/nivea.jpg') center top no-repeat; background-color: white;\">
				<a class=\"branding\" href=\"http://media.easyads.bg/cpm_click.php?md5checksum=LEWL6qil-4a1Kj7sF0_1pmt9mwMigINAKC1rAdVcqTeY0E9_0arHCLJ_rlRvMGFREfeI3R0HUMKpOl9iQUyY8wEhiEVo5OZD60FyqvaJwZi7dDRe6LqwL-4W5k0YdoaJFDPgKA4UtFhzvhLGUqJCsxxBbofUn1Yo_AJvUFsvBag,&newurl=\" target=\"_blank\" 
				style=\"display: block;
				                 height: 1400px;
								width: 100%;
								position: fixed;
								\">
				<span style=\"display: none;\">nivea</span>
				</a> 
				<script type=\"text/javascript\"><!--//<![CDATA[
				/*  AdMetrics Campaign: Nivea Creme Share Love / Feb'12 , url: programata.bg */
				var dd=et=dc=ur=rf=sc=scr=\"\";var cb=0;scr=screen;dd=new Date();dc=document;et=dd.getTimezoneOffset()+dd.getTime();sc=scr.width+\"x\"+scr.height;cb=Math.round(Math.random()*21474836);rf=escape(dc.referrer);ur=\"ht\"+\"tp://media.easyads.bg/cpm.php?qid=1446&scr=\"+sc+\"&et=\"+et+\"&cb=\"+cb+\"&rf=\"+rf+\"&md5checksum=yu4--qA6z4dHqMytwC2HyQ,,&eaclick=\";dc.writeln(\"<sc\"+\"ript src=\\\"\"+ur+\"\\\" type=\\\"text/jav\"+\"ascr\"+\"ipt\\\"></scr\"+\"ipt>\");
				//]]>--></script>
				";
			
			$sSQL = "INSERT INTO impressions (id,name,day, impressions) VALUES (20, 'nivea', CURDATE(), 1)
			  ON DUPLICATE KEY UPDATE impressions=impressions+1";
			
			$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
			dbAssert($result, $sSQL);
		}
		elseif($rand <= 40)
		 {
			echo "
				<body id=\"body\" class=\"".$class."\" style=\"background: url('/img/fashiondays.jpg') center top no-repeat; background-color: white;\">
				<a class=\"branding\" href=\"http://www.fashiondays.bg/shop/customer/account/login/\" target=\"_blank\" 
				style=\"display: block;
				                 height: 1400px;
								width: 100%;
								position: fixed;
								\">
				<span style=\"display: none;\">Fashion Days</span>
				</a> 
				";
			
			$sSQL = "INSERT INTO impressions (id,name,day, impressions) VALUES (21, 'FashionDays', CURDATE(), 1)
			  ON DUPLICATE KEY UPDATE impressions=impressions+1";
			
			$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
			dbAssert($result, $sSQL);
		}
		else
		{
			echo "<body id=\"body\" class=\"".$class."\">";
		}
	}

}

?>