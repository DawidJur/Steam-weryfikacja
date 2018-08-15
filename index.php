<meta charset="utf-8">
<title>Steam Games checking ID</title>
<form action="" method="get">
	<p>Nick, URL after /id/ <input type="text" name="id"> <input type="submit"></p>
	<?php
class TaskVerification {
	public $GameSteamID = "690610";
	
	private function SteamFinderURL(){
		$this->steamFinderURL = "https://steamid.io/lookup/";
		$this->id = $_GET['id'];
		return ($this->steamFinderURL.$this->id = $_GET['id']);
	}
	
	public function getPlayerID64() {
		$this->SteamFinderURL = $this->SteamFinderURL();
		$this->curl = curl_init();
		curl_setopt($this->curl,CURLOPT_URL,$this->SteamFinderURL);
		curl_setopt($this->curl,CURLOPT_RETURNTRANSFER,1);
		$this->SteamFinderHTML = curl_exec($this->curl);
		curl_close($this->curl);
		$this->tab1 = explode("steamID64</dt>", $this->SteamFinderHTML);
		$this->tab2 = explode("data-clipboard-text=\"", $this->tab1[1]);
		$this->tab3 = explode("\" src=\"", $this->tab2[1]);
		$this->GetPlayerID64 = $this->tab3[0];
		$this->PlayerID64 = $this->GetPlayerID64;
		return $this->GetPlayerID64;
	}
	
	public $PlayerID64;
	
 private function getTimeJSON() {
		$this->SteamAPIUrl = "http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=BB63045D0A5A74428B323EC16FB987EF&steamid=".$this->PlayerID64."&format=json";
		$this->curl = curl_init();
		curl_setopt($this->curl,CURLOPT_URL,$this->SteamAPIUrl);
		curl_setopt($this->curl,CURLOPT_RETURNTRANSFER,1);
		$this->APIJson = curl_exec($this->curl);
		curl_close($this->curl);
		$this->json = json_decode($this->APIJson);
		return $this->json;
	}
	
	public function getPlayTime() {
		$this->gameArrays = $this->getTimeJSON()->response->games;
		if(!empty($this->gameArrays)){
			foreach ($this->gameArrays as $table) {
				if($table->appid == $this->GameSteamID){
					return $table->playtime_forever;
				}
			}
		}	
	}
	
	private function getAchievementsJSON() {
		$this->SteamAPIUrl = "http://api.steampowered.com/ISteamUserStats/GetPlayerAchievements/v0001/?appid=690610&key=BB63045D0A5A74428B323EC16FB987EF&steamid=".$this->PlayerID64;
		$this->curl = curl_init();
		curl_setopt($this->curl,CURLOPT_URL,$this->SteamAPIUrl);
		curl_setopt($this->curl,CURLOPT_RETURNTRANSFER,1);
		$this->APIJson = curl_exec($this->curl);
		curl_close($this->curl);
		$this->json = json_decode($this->APIJson);
		return $this->json; 
	}
	
	public function getAchievements($AchievementTasks) {
		$this->AchievementsArray = $this->getAchievementsJSON()->playerstats->achievements;
		$this->getFeedback = "";
		if(!empty($this->AchievementsArray)){
			foreach($AchievementTasks as $AchievementName){
				foreach ($this->AchievementsArray as $table) {
					if($table->apiname == $AchievementName){
						$this->getFeedback .= "<br />Achievement ".$AchievementName." = ".$table->achieved;
					}
				}
			}
		}
		return $this->getFeedback;
	}
	
	public function getFeedback($Array) {
		return ("User ID64: ".$this->getPlayerID64()."<br /> 
		Playtime is: ".$this->getPlayTime()." minutes
		".$this->getAchievements($Array));
	}
}

$v = new TaskVerification;
$Achievements = Array("JudgeBronze", "AssassinBronze", "ArtistBronze", "ProgrammerBronze", "GreedSilver");
echo($v->getFeedback($Achievements));

?>
