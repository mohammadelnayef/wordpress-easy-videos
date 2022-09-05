<?php
namespace Plugin\EasyVideos;

class YoutubeAPI{

    private string $nextPageToken;
   
    // Entry point in the class, fetches data via the youtube api, if there is a page token passed to the function
    // it will search for that page, if not the latest page will be fetched.
    // After the data is fetched then it's processed and returned for external use.
    public function fetchVideosData(string $pageToken = ''){
        $data = $this->getAPIData($pageToken);
        return $this->processAPIData($data);
    }

    private function getAPIData(string $pageToken): string {
        $url = "https://www.googleapis.com/youtube/v3/search?key=AIzaSyDwGJvLxXp0b9t_FS2-r_9QWna1Kbp2Dhw&channelId=UCXuqSBlHAE6Xw-yeJA0Tunw&part=snippet,id&order=date&maxResults=1&pageToken=$pageToken";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function setNextPageToken(string $token){
        $this->nextPageToken = $token;
    }

    public function getNextPageToken(): string {
        return $this->nextPageToken;
    }

    // Processes the data and assigns the nextPageToken
    private function processAPIData(string $jsonData): array {
        $arrayData = json_decode($jsonData,true);
        $data = [
          'nextPageToken' => $arrayData["nextPageToken"],
          'videoTitle' => $arrayData["items"][0]["snippet"]["title"],
          'videoDescription' => $arrayData["items"][0]["snippet"]["description"],
          'videoPublishDate' => $arrayData["items"][0]["snippet"]["publishedAt"],
          'videoThumbnailURL' => $arrayData["items"][0]["snippet"]["thumbnails"]["medium"]["url"],
          'videoURL' => 'https://www.youtube.com/watch?v='.$arrayData["items"][0]["id"]["videoId"],
          'videoID' => $arrayData["items"][0]["id"]["videoId"],
          'channelName' => $arrayData["items"][0]["snippet"]['channelTitle'],
          'abChannelName' => str_replace(' ','',$arrayData["items"][0]["snippet"]['channelTitle'])
        ];
        $this->nextPageToken = $arrayData["nextPageToken"];
        return $data;
    }
}
