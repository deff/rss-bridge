<?php
/**
* RssBridgeDollbooru
* Returns images from given page
*
* @name Dollbooru
* @description Returns images from given page
* @use1(p="page", t="tags")
*/
class DollbooruBridge extends BridgeAbstract{

    public function collectData(array $param){
	$page = 0;$tags='';
        if (isset($param['p'])) { 
            $page = (int)preg_replace("/[^0-9]/",'', $param['p']); 
        }
        if (isset($param['t'])) { 
            $tags = urlencode($param['t']); 
        }
        $html = file_get_html("http://dollbooru.org/post/list/$tags/$page") or $this->returnError('Could not request Dollbooru.', 404);


	foreach($html->find('div[class=shm-image-list] center[class=shm-thumb]') as $element) {
		$item = new \Item();
		$item->uri = 'http://dollbooru.org'.$element->find('a', 0)->href;
		$item->postid = (int)preg_replace("/[^0-9]/",'', $element->getAttribute('data-post-id'));	
		$item->timestamp = time();
		$item->thumbnailUri = 'http://dollbooru.org'.$element->find('img', 0)->getAttribute('data-original');
		$item->tags = $element->getAttribute('data-tags');
		$item->title = 'Dollbooru | '.$item->postid;
		$item->content = '<a href="' . $item->uri . '"><img src="' . $item->thumbnailUri . '" /></a><br>Tags: '.$item->tags;
		$this->items[] = $item; 
	}
    }

    public function getName(){
        return 'Dollbooru';
    }

    public function getURI(){
        return 'http://dollbooru.org/';
    }

    public function getCacheDuration(){
        return 1800; // 30 minutes
    }
}
