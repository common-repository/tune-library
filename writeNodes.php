<?php

if( isset( $_GET['parentId'] ) ){
	$incoming = rawurldecode( $_GET['parentId'] );
	list( $pre, $itemData, $itemData2 ) = explode('::', $incoming);
	
	require_once( '../../../wp-load.php' );
	global $wpdb;
	
	if( $pre == 'artist' ){
		$querystr = 'SELECT distinct album FROM ' . $wpdb->prefix . "tracks where artist = '%s' order by album";

		$querystr = $wpdb->prepare( $querystr, stripslashes( $itemData ) );
		$tracks = $wpdb->get_results( $querystr );
		
		foreach($tracks as $track){
			echo "<li><a href='#'> ".$track->album."</a>
				<ul>
					<li parentId='album::".urlencode($itemData)."::".urlencode($track->album)."'><a href='#'>Loading...</a></li>
				</ul>			
			</li>";
		}
	}
	
	if( $pre == 'albumartist' ){
		$querystr = 'SELECT distinct album FROM ' . $wpdb->prefix . "tracks where albumartist = '%s' order by album";
		$querystr = $wpdb->prepare( $querystr, stripslashes( $itemData ) );
		$tracks = $wpdb->get_results( $querystr );
		
		foreach($tracks as $track){
			echo "<li><a href='#'> ".$track->album."</a>
				<ul>
					<li parentId='albumvarious::".urlencode($itemData)."::".urlencode($track->album)."'><a href='#'>Loading...</a></li>
				</ul>			
			</li>";
		}
	}	
	
	if( $pre == 'album' ) {
		$querystr = "SELECT * FROM " . $wpdb->prefix . "tracks where artist= '%s' and album = '%s'";
		$querystr = $wpdb->prepare( $querystr, stripslashes( $itemData ), stripslashes( $itemData2 ) );
		$tracks = $wpdb->get_results( $querystr, ARRAY_A );

		$sort = array();
		foreach( $tracks as $k => $v ) {
			if ( isset( $v['diskid'] ) ) {
				$sort['diskid'][ $k ] = $v['diskid'];
			}
			$sort['tracknum'][$k] = $v['tracknum'];
		}

		if ( empty( $sort['diskid'] ) ) {
			array_multisort( $sort['tracknum'], SORT_ASC, $tracks );
		} else {
			array_multisort( $sort['diskid'], SORT_ASC, $sort['tracknum'], SORT_ASC, $tracks );
		}

		$disk_id_array = array();
		foreach( $tracks as $track ) {
			if ( isset( $track['diskid'] ) && !isset( $disk_id_array[$track['diskid']] ) ) {
				$disk_id_array[$track['diskid']] = 1;
			}
		}
	
		foreach( $tracks as $track ) {
			if( isset( $track['tracknum'] ) ) {
				echo '<li class="dhtmlgoodies_sheet.gif"><a href="#" disabled></a> ';
				if ( isset( $track['diskid'] ) && ( 1 != $track['diskid'] || count( $disk_id_array ) > 1 ) ) {
					echo $track['diskid'] . ' - ';
				}
				echo $track['tracknum'] . ' - ' . $track['title'] . '</li>';
			} else {
				echo "<li class='dhtmlgoodies_sheet.gif'><a href='#' disabled></a> " . $track['title'] . '</li>';
			}
		}
	}
	
	if ( $pre == 'albumvarious' ) {
		$querystr = 'SELECT * FROM ' . $wpdb->prefix . "tracks where albumartist= '%s' and album = '%s'";
		$querystr = $wpdb->prepare( $querystr, stripslashes( $itemData ), stripslashes( $itemData2 ) );
		$tracks = $wpdb->get_results( $querystr, ARRAY_A );

		# get a list of sort columns and their data to pass to array_multisort
		$sort = array();
		foreach( $tracks as $k => $v ) {
			if ( isset( $v['diskid'] ) ) {
				$sort['diskid'][$k] = $v['diskid'];
			}

			$sort['tracknum'][$k] = $v['tracknum'];
		}

		if ( empty( $sort['diskid'] ) ) {
			array_multisort( $sort['tracknum'], SORT_ASC, $tracks );
		} else {
			array_multisort( $sort['diskid'], SORT_ASC, $sort['tracknum'], SORT_ASC, $tracks );
		}


		$disk_id_array = array();
		foreach ( $tracks as $track ) {
			if ( isset( $track['diskid'] ) && !isset( $disk_id_array[$track['diskid']] ) ) {
				$disk_id_array[$track['diskid']] = 1;
			}
		}
	
		foreach( $tracks as $track ) {
			if ( isset( $track['tracknum'] ) ){
				echo '<li class="dhtmlgoodies_sheet.gif"><a href="#" disabled></a> ';
				if ( isset( $track['diskid'] ) && ( 1 != $track['diskid'] || count( $disk_id_array ) > 1 ) ) {
					echo $track['diskid'] . ' - ';
				}
				echo $track['tracknum'] . ' - ' . $track['artist'] . ' - ' . $track['title'] . '</li>';
			} else {
				echo '<li class="dhtmlgoodies_sheet.gif"><a href="#" disabled></a> ' . $track['title'] . '</li>';
			}
		}
	}
}

?>