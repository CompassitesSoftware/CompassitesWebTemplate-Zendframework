<?php
/**
 * @file		Importfeeds.php
 *
 * @category   	Compassites
 * @package    	Compassites_Api
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief		Import Feeds from Twitter, Flickr and YouTube
 *
 * @package    	Compassites_Api
 * @class		Compassites_Api_Importfeeds
 * @see
 */
class Compassites_Api_Importfeeds
{
    /**
     * @brief	feedDetails
     *
     * @var 	array
     */
    protected $_feeds = array();


	public function __construct()
	{
		$this->importFeeds();
	}

	public function get($page, $records='10')
	{
		$startFrom 	= $page * $records;
		$feedsPart	= array();

		return $this->buildFeeds($feedsPart = array_slice($this->_feeds,$startFrom, $records));
	}

	/**
	 * @internal Constructor which import the feeds from Twitter,Flickr and YouTube and
	 */
	private function importFeeds()
	{
		$cacheKey	= APPLICATION_NAME."_FEEDS";
		$cacheObj	= Zend_Registry::get('cache');

		if(!$this->_feeds	= $cacheObj->load($cacheKey))
		{
			$feedUrls	= array(
							//"TWITTER"	=> "http://api.twitter.com/1/statuses/user_timeline.rss?screen_name=thenextweb",
							"TWITTER" => "http://api.twitter.com/1/statuses/user_timeline.rss?screen_name=compassitesonlin",
							//"FLICKR" 	=> "http://api.flickr.com/services/feeds/photos_public.gne",
							"FLICKR" 	=> "http://api.flickr.com/services/feeds/photos_public.gne?id=77060636@N04&lang=en-us&format=rss_200",
							//"YOUTUBE"	=> "http://gdata.youtube.com/feeds/api/videos?orderby=updated&vq=compassites");
							"YOUTUBE"	=> "http://gdata.youtube.com/feeds/api/videos?orderby=updated&vq=compassitesonline");

			$currentDate= new Zend_Date();

			foreach($feedUrls as $feedKey=>$feedUrl)
			{
				$feedData	= Zend_Feed_Reader::import($feedUrl);

				foreach ($feedData as $entry)
				{
					$feedArray 					= array();
					$feedArray['title']			= $entry->getTitle();
					$feedArray['description']	= $entry->getDescription();
					$feedArray['dateCreated']	= $entry->getDateCreated();
					$feedArray['authors']      	= $entry->getAuthors();
					$feedArray['link']			= $entry->getLink();
					$feedArray['content']      	= $entry->getContent();

					$feedArray['timeAgo']		= self::dateDiff($feedArray['dateCreated']);
					$feedArray['feedOwner']		= $feedKey;

					if('TWITTER' == $feedKey)
					{
						preg_match('/http:\/\/[www.]?.[^\s]*/', $feedArray['content'], $hasLink);

						if($hasLink[0] != '')
						{
							$feedArray['content']	= preg_replace('/http:\/\/[www.]?.[^\s]*/','<a href="$0" target="_blank">$0</a>',$feedArray['content']);
						}
						else
						{
							$feedArray['content'] = "<a href={$feedArray['link']}>{$feedArray['content']}</a>";
						}
					}

					if('FLICKR' == $feedKey)
					{
						$matches				= array();
						preg_match('/<img\s*.*\/\>/', $feedArray['content'], $matches);
						# remove the width and height
						$flickrImg				= preg_replace('/width\=\"[0-9]*\"/', '', $matches[0]);
						$flickrImg				= preg_replace('/height\=\"[0-9]*\"/', '', $matches[0]);
						$feedArray['FlickImage']= $flickrImg;
					}

					$feedArray['feedCreatedOn']	= new DateTime((string) $feedArray['dateCreated']);
					$this->_feeds[]				= $feedArray;
				}
			}

			$this->_feeds = $this->arrayValueSort($this->_feeds, 'feedCreatedOn');

			$cacheObj->save($this->_feeds, $cacheKey);
		}
	}

	/**
	 * @internal This Function receives the Twitter,Flickr and YouTube feeds then build the html part based on that.
	 * @param 	Object Feed details
	 *
	 * @return 	Array HTML Structure
	 */
	private function buildFeeds($feeds)
	{
		$feedHtmlString	= array();
		$twitterFeedId	= 0;

		foreach($feeds as $feedKey=>$feedVal)
		{
			if('TWITTER' == $feedVal['feedOwner'])
			{
				$twitterFeedId	= explode("/",$feedVal['link']);
				$twitterFeedId	= $twitterFeedId[count($twitterFeedId)-1];

				$feedHtmlString[] = "<li class='for-twitter'>
	<img width='8' height='10' class='feed-icons' alt='Twitter' title='Twitter' src='images/launch/icon-twitter.png' />
	{$feedVal['content']}
	<div class='twitter-actions'>
		<!--<a title='Follow' class='icon-follow' href='https://twitter.com/compassitesonlin' target='_blank' class='twitter-follow-button' data-show-count='false' data-show-screen-name='false'>
		</a>-->
		<a title='Reply' class='icon-reply' href='https://twitter.com/intent/tweet?in_reply_to={$twitterFeedId}'></a>
		<a title='Favourite' class='icon-favourite' href='https://twitter.com/intent/favorite?tweet_id={$twitterFeedId}'></a>
		<a title='Retweet' class='icon-retweet' href='https://twitter.com/intent/retweet?tweet_id={$twitterFeedId}'></a>

	</div>
	<span>{$feedVal['timeAgo']}</span>
</li>";
			}

			if('FLICKR' == $feedVal['feedOwner'])
			{
				$feedHtmlString[] = "<li class='for-flickr'>
	<img width='17' height='9' class='feed-icons' alt='Flickr' title='Flickr' src='images/launch/icon-flickr.png' />
	<a href='{$feedVal['link']}' target='_blank'>{$feedVal['FlickImage']}</a>
	<span>{$feedVal['timeAgo']}</span>
</li>";
			}

			if('YOUTUBE' == $feedVal['feedOwner'])
			{
				$feedHtmlString[] = "<li class='for-youtube'>
	<img width='9' height='10' class='feed-icons' alt='Youtube' title='Youtube' src='images/launch/icon-youtube.png' />
	<a href='{$feedVal['link']}' target='_blank'>
		<object wmode='transparent' width='175' height='116'>
			<param name='movie' value='{$feedVal['link']}?version=3&autohide=1&showinfo=0'></param>
			<param name='allowFullScreen' value='true'></param>
			<param name='allowscriptaccess' value='always'></param>
			<embed wmode='transparent' src='{$feedVal['link']}?version=3&autohide=1&showinfo=0' type='application/x-shockwave-flash' width='175' height='116' allowscriptaccess='always'></embed>
		</object>
	</a>
	<span>{$feedVal['timeAgo']}</span>
</li>";

				/*$feedHtmlString	.= '<li class="icon-flickr">
					<object width="175" height="116"><param name="movie" value="'.$feedVal['link'].'"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="'.$feedVal['link'].'" type="application/x-shockwave-flash" width="175" height="116" allowscriptaccess="always" allowfullscreen="true"></embed></object>
					<span>'.$feedVal['timeAgo'].'</span></li>';*/
			}
		}
		$feedHtmlString['totalRecords']	= count($this->_feeds);

		return json_encode($feedHtmlString);
	}

	private function arrayValueSort($data, $key)
	{
		$sortDate = array();

		foreach($data as $key=>$value)
		{
			$sortDate[] = $value['feedCreatedOn'];
		}

		array_multisort($sortDate, SORT_DESC, $data);

		return $data;
	}

	private static function dateDiff($date)
	{
		$ago		= '0 hour ago';

		$date1 		= new DateTime((string) $date);
		$date2 		= new DateTime("now");

		$interval 	= $date1->diff($date2);
		$years 		= $interval->format('%y');
		//$months 	= $interval->format('%m');
		# days not limited to 30
		$days 		= $interval->format('%a');
		$hours 		= $interval->format('%h');

		if( $years != 0)
		{
			$ago 	= $years.' year(s) ago';
		}
		elseif( $days != 0 )
		{
			$ago 	= $days.' day(s) ago';
		}
		elseif( $hours != 0 )
		{
			$ago 	= $hours.' hour(s) ago';
		}

		return $ago;
	}
};