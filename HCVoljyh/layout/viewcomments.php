<?php
require_once("Menus.php") ;
function DisplayComments($m,$TCom) {
  global $title ;
  $title=ww('ViewComments') ;
  include "header.php" ;
	
	Menu1() ; // Displays the top menu

	Menu2($_SERVER["PHP_SELF"]) ;
// Header of the profile page
  require_once("profilepage_header.php") ;

echo "	<div id=\"columns\">" ;
menumember("viewcomments.php?cid=".$m->id,$m->id,$m->NbComment) ;
echo "		<div id=\"columns-low\">" ;


ShowActions("<li><a href=\"addcomments.php?cid=".$m->id."\">",ww("addcomments"),"</a></li>") ;
ShowAds() ; // Show the Ads

echo "			<div class=\"clear\" />" ;
  echo "\n<center>\n" ;
  echo "<table>\n" ;


	$iiMax=count($TCom) ;
	$tt=array() ;
	for ($ii=0;$ii<$iiMax;$ii++) {
	  $color="black" ;
	  if ($TCom[$ii]->Quality=="Good") {
		  $color="#808000" ;
		}
	  if ($TCom[$ii]->Quality=="Bad") {
		  $color="red" ;
		}
    echo "<tr><td valign=center>" ;
		echo "<ul>" ;
		echo "<li>" ;
    echo "<b>",ww("CommentFrom",$TCom[$ii]->Commenter),"</b><br><br>" ;
		echo "<li>" ;
		echo "</li>" ;
    echo "<i>",$TCom[$ii]->TextWhere,"</i>" ;
    echo "<br><font color=$color>",$TCom[$ii]->TextFree,"</font>" ;
		echo "</li>" ;
		echo "</ul>" ;
    echo "</td>" ;
		$tt=explode(",",$TCom[$ii]->Lenght) ;
		echo "<td>" ;
		for ($jj=0;$jj<count($tt);$jj++) {
		  echo "&nbsp;&nbsp;&nbsp;<li>",ww("Comment_".$tt[$jj]),"</li><br>" ;
		} 
		
		if (HasRight("Comments")) echo " <a href=\"admincomments.php?action=editonecomment&IdComment=",$TCom[$ii]->id,"\">edit</a>" ;
		echo "</td>" ;
	}
  
  echo "</table>\n" ;
	
  echo "</center>\n" ;
echo "					<div class=\"user-content\">" ;
  include "footer.php" ;
echo "					</div>" ;
}

?>
