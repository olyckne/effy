<?php

function calculateWidth(&$hasSidebar1, &$hasSidebar2, &$classSidebar1, &$classSidebar2, &$classContent) {
	$theme = Theme::GetInstance();

	$hasSidebar1 = $theme->viewExist('sidebar1');
	$hasSidebar2 = $theme->viewExist('sidebar2');

	if($hasSidebar1 && $hasSidebar2) {
		$classContent	= "span-16 border";
		$classSidebar1	= "span-4 border";
		$classSidebar2	= "span-4 last"; 
	} elseif($hasSidebar1) {
		$classContent	= "span-19 last";
		$classSidebar1	= "span-4 colborder";
		$classSidebar2	= null;
	} elseif($hasSidebar2) {
		$classContent	= "span-19 colborder";
		$classSidebar1	= null;
		$classSidebar2	= "span-4 last";	
	}

}