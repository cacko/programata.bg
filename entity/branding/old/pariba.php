<?php

class pariba extends BrandingItem {

	protected $_startDate = '11/8/2011';
	protected $_endDate = '11/10/2011';

	protected function _isSuitable() {
		global $page, $nRootPage;
		
		$rand = mt_rand(1, 100);
		//return ($rand <= 15) ;
		return ($page == 149);
	}

	public function display() {
			echo "<body id=\"body\" style=\"background: url('/img/pariba.jpg') center top no-repeat; background-color: #a3b9ce;\">
				<a href=\"http://bg.hit.gemius.pl/hitredir/id=nGrg6wSiPw1CfUp2BR0tdOWiHUcpGyOo_h6P8YHPTz..X7/stparam=vnemkjildf/url=http://www.bnpparibas-pf.bg/Apply_online.aspx\" target=\"_blank\" 
				style=\"display: block;
				                 height: 1400px;
								width: 100%;
								position: fixed;
								\">
				<span style=\"display: none;\">Paribas Kredit Plan</span>
				</a> 
				<script language=\"javascript1.2\" type=\"text/javascript\">
				//<![CDATA[
				_gde_ymnfhmogik = new Image(1,1);
				_gde_ymnfhmogik.src='http://bg.hit.gemius.pl/_'+(new Date()).getTime()+'/redot.gif?id=nGrg6wSiPw1CfUp2BR0tdOWiHUcpGyOo_h6P8YHPTz..X7/stparam=ymnfhmogik';
				//]]>
				</script>";
			
			$sSQL = "INSERT INTO impressions (id,name,day, impressions) VALUES (14, 'pariba', CURDATE(), 1)
			  ON DUPLICATE KEY UPDATE impressions=impressions+1";
			
			$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
			dbAssert($result, $sSQL);
	}

}

?>