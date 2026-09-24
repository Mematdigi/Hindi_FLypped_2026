<?php
namespace App\Controllers;

class WorldcupController extends BaseController
{
    /**
     * Display homepage with all sections
     */
    public function index()
    {
        // Fetch cricket match data
        $cricket_match = $this->getCricketMatchInfo();
        
        // Prepare data for view
        $data = [
            'cricket_match' => $cricket_match,
            'page_title' => 'Home - Flypped',
        ];
        
        // Load the index view
        return view('index', $data);
    }
    
    /**
     * Convert GMT datetime to IST format (12-hour clock)
     * @param string $gmtDateTime GMT datetime string
     * @return array Returns formatted date, time, and full datetime in IST
     */
    private function convertToIST($gmtDateTime)
    {
        if (empty($gmtDateTime)) {
            return [
                'date' => '',
                'time' => '',
                'datetime' => '',
                'formatted_status' => ''
            ];
        }
        
        try {
            // Create DateTime object in GMT
            $dateTime = new \DateTime($gmtDateTime, new \DateTimeZone('GMT'));
            
            // Convert to IST (Asia/Kolkata)
            $dateTime->setTimezone(new \DateTimeZone('Asia/Kolkata'));
            
            return [
                'date' => $dateTime->format('Y-m-d'), // 2026-02-18
                'time' => $dateTime->format('h:i A'), // 11:00 AM
                'datetime' => $dateTime->format('Y-m-d H:i:s'), // Full datetime
                'formatted_status' => 'Match starts at ' . $dateTime->format('M d, h:i A') . ' IST'
            ];
        } catch (\Exception $e) {
            log_message('error', 'DateTime conversion error: ' . $e->getMessage());
            return [
                'date' => '',
                'time' => '',
                'datetime' => '',
                'formatted_status' => ''
            ];
        }
    }
    
    /**
     * Format match data with IST conversion
     * @param array $match Match data
     * @return array Formatted match data
     */
    private function formatMatchWithIST($match)
    {
        if (isset($match['dateTimeGMT'])) {
            $istData = $this->convertToIST($match['dateTimeGMT']);
            
            // Add IST fields to match data
            $match['dateIST'] = $istData['date'];
            $match['timeIST'] = $istData['time'];
            $match['dateTimeIST'] = $istData['datetime'];
            
            // Update status if match hasn't started
            if (isset($match['matchStarted']) && !$match['matchStarted'] && !empty($istData['formatted_status'])) {
                $match['statusIST'] = $istData['formatted_status'];
            }
        }
        
        return $match;
    }
    
    /**
     * Fetch live cricket match details from API
     * @return array|null Returns match data or null on failure
     */
    public function getCricketMatchInfo()
    {
        // Get match ID from query parameter
        $matchId = $this->request->getGet('id');
        
        if (empty($matchId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Match ID is required. Please provide id parameter.',
                'data' => null
            ]);
        }
        
        // API Configuration
        $apiKey = '9bafa7bc-e94e-45de-818e-2cf8c7404f5e';
        $apiUrl = "https://api.cricapi.com/v1/match_info?apikey={$apiKey}&id={$matchId}";
        
        try {
            // Initialize cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            // Execute request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            // Check for cURL errors
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                log_message('error', '🏏 Cricket API cURL Error: ' . $error);
                return null;
            }
            
            curl_close($ch);
            
            // Check HTTP status code
            if ($httpCode !== 200) {
                log_message('error', '🏏 Cricket API HTTP Error: ' . $httpCode);
                return null;
            }
            
            // Decode JSON response
            $data = json_decode($response, true);
            
            // Validate response
            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', '🏏 Cricket API JSON Error: ' . json_last_error_msg());
                return null;
            }
            
            // Check if data exists
            if (!isset($data['data'])) {
                log_message('error', '🏏 Cricket API: No data in response');
                return null;
            }
            
            // Format match data with IST conversion
            $data['data'] = $this->formatMatchWithIST($data['data']);
            
            // Log success
            log_message('info', '🏏 Cricket API: Successfully fetched match data');
            
            return $data['data'];
            
        } catch (\Exception $e) {
            // Log exception
            log_message('error', '🏏 Cricket API Exception: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Fetch list of cricket matches (for future use)
     * @param int $offset Starting position
     * @param int $limit Number of matches to fetch
     * @return array|null Returns matches list or null on failure
     */
    public function getCricketMatches($offset = 0, $limit = 5)
    {
        $apiKey = '9bafa7bc-e94e-45de-818e-2cf8c7404f5e';
        $apiUrl = "https://api.cricapi.com/v1/matches?apikey={$apiKey}&offset={$offset}&limit={$limit}";
        
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_errno($ch)) {
                curl_close($ch);
                return null;
            }
            
            curl_close($ch);
            
            if ($httpCode !== 200) {
                return null;
            }
            
            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || !isset($data['data'])) {
                return null;
            }
            
            // Format each match with IST conversion
            if (is_array($data['data'])) {
                foreach ($data['data'] as &$match) {
                    $match = $this->formatMatchWithIST($match);
                }
            }
            
            return $data['data'];
            
        } catch (\Exception $e) {
            log_message('error', '🏏 Cricket Matches API Exception: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * API endpoint to get cricket match info (for AJAX calls)
     * @return JSON response
     */
    public function apiGetMatchInfo()
    {
        $matchData = $this->getCricketMatchInfo();
        
        if ($matchData) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $matchData
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to fetch cricket match data'
            ])->setStatusCode(500);
        }
    }
    
    /**
     * API endpoint to get cricket matches list (for AJAX calls)
     * @return JSON response
     */
    public function apiGetMatches()
    {
        $offset = $this->request->getGet('offset') ?? 0;
        $limit = $this->request->getGet('limit') ?? 5;
        
        $matchesData = $this->getCricketMatches($offset, $limit);
        
        if ($matchesData) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $matchesData
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to fetch cricket matches data'
            ])->setStatusCode(500);
        }
    }
    
    /**
     * Fetch series information with matches sorted by date proximity
     * @param string $seriesId Series ID (default: ICC Men's T20 World Cup 2026)
     * @return array|null Returns series data with sorted matches or null on failure
     */
    public function getSeriesInfo($seriesId = '87c62aac-bc3c-4738-ab93-19da0690488f')
    {
        $apiKey = '9bafa7bc-e94e-45de-818e-2cf8c7404f5e';
        $apiUrl = "https://api.cricapi.com/v1/series_info?apikey={$apiKey}&id={$seriesId}";
        
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_errno($ch)) {
                curl_close($ch);
                return null;
            }
            
            curl_close($ch);
            
            if ($httpCode !== 200) {
                return null;
            }
            
            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || !isset($data['data'])) {
                return null;
            }
            
            // Format each match with IST conversion
            if (isset($data['data']['matchList']) && is_array($data['data']['matchList'])) {
                foreach ($data['data']['matchList'] as &$match) {
                    $match = $this->formatMatchWithIST($match);
                }
                
                // Sort matches by date proximity
                $data['data']['matchList'] = $this->sortMatchesByDateProximity($data['data']['matchList']);
            }
            
            return $data['data'];
            
        } catch (\Exception $e) {
            log_message('error', '🏏 Series Info API Exception: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Sort matches by proximity to current date
     * Live matches first, then upcoming matches (nearest first), then completed matches (most recent first)
     * @param array $matches Array of match data
     * @return array Sorted matches array
     */
    private function sortMatchesByDateProximity($matches)
    {
        $currentDateTime = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $currentDateStr = $currentDateTime->format('Y-m-d');
        
        // Categorize matches
        $liveMatches = [];
        $todayMatches = [];
        $upcomingMatches = [];
        $completedMatches = [];
        
        foreach ($matches as $match) {
            // Use IST date if available, otherwise use original date
            $matchDate = $match['dateIST'] ?? $match['date'] ?? '';
            $matchStarted = $match['matchStarted'] ?? false;
            $matchEnded = $match['matchEnded'] ?? false;
            
            // Live matches (started but not ended)
            if ($matchStarted && !$matchEnded) {
                $liveMatches[] = $match;
            }
            // Today's matches (not started yet)
            elseif ($matchDate === $currentDateStr && !$matchStarted) {
                $todayMatches[] = $match;
            }
            // Completed matches
            elseif ($matchEnded) {
                $completedMatches[] = $match;
            }
            // Upcoming matches
            else {
                $upcomingMatches[] = $match;
            }
        }
        
        // Sort upcoming matches by date (nearest first)
        usort($upcomingMatches, function($a, $b) {
            $dateA = strtotime($a['dateTimeGMT'] ?? $a['date']);
            $dateB = strtotime($b['dateTimeGMT'] ?? $b['date']);
            return $dateA - $dateB;
        });
        
        // Sort completed matches by date (most recent first)
        usort($completedMatches, function($a, $b) {
            $dateA = strtotime($a['dateTimeGMT'] ?? $a['date']);
            $dateB = strtotime($b['dateTimeGMT'] ?? $b['date']);
            return $dateB - $dateA;
        });
        
        // Sort today's matches by time
        usort($todayMatches, function($a, $b) {
            $dateA = strtotime($a['dateTimeGMT']);
            $dateB = strtotime($b['dateTimeGMT']);
            return $dateA - $dateB;
        });
        
        // Sort live matches by start time (most recent first)
        usort($liveMatches, function($a, $b) {
            $dateA = strtotime($a['dateTimeGMT'] ?? $a['date']);
            $dateB = strtotime($b['dateTimeGMT'] ?? $b['date']);
            return $dateB - $dateA;
        });
        
        // Merge all arrays: Live -> Today -> Upcoming -> Completed
        return array_merge($liveMatches, $todayMatches, $upcomingMatches, $completedMatches);
    }
    
    /**
     * API endpoint to get series info with sorted matches
     * @return JSON response
     */
    public function apiGetSeriesInfo()
    {
        $seriesId = $this->request->getGet('id') ?? '87c62aac-bc3c-4738-ab93-19da0690488f';
        $seriesData = $this->getSeriesInfo($seriesId);
        
        if ($seriesData) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $seriesData
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to fetch series data'
            ])->setStatusCode(500);
        }
    }
    
    /**
     * API endpoint to get upcoming matches (next matches by date)
     * @return JSON response
     */
    public function apiGetUpcomingMatches()
    {
        $seriesId = $this->request->getGet('id') ?? '87c62aac-bc3c-4738-ab93-19da0690488f';
        $limit = $this->request->getGet('limit') ?? 10;
        
        $seriesData = $this->getSeriesInfo($seriesId);
        
        if ($seriesData && isset($seriesData['matchList'])) {
            // Filter only upcoming and today's matches
            $currentDateTime = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
            $currentDateStr = $currentDateTime->format('Y-m-d');
            
            $upcomingMatches = array_filter($seriesData['matchList'], function($match) use ($currentDateStr) {
                $matchDate = $match['dateIST'] ?? $match['date'] ?? '';
                $matchEnded = $match['matchEnded'] ?? false;
                
                // Include today's matches and future matches that haven't ended
                return !$matchEnded && $matchDate >= $currentDateStr;
            });
            
            // Limit the results
            $upcomingMatches = array_slice(array_values($upcomingMatches), 0, $limit);
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'info' => $seriesData['info'] ?? [],
                    'matchList' => $upcomingMatches,
                    'total' => count($upcomingMatches)
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to fetch upcoming matches'
            ])->setStatusCode(500);
        }
    }
    
    /**
     * API endpoint to get live and today's matches
     * @return JSON response
     */
    public function apiGetTodayMatches()
    
{
    $seriesId = $this->request->getGet('id') ?? '87c62aac-bc3c-4738-ab93-19da0690488f';
    
    $seriesData = $this->getSeriesInfo($seriesId);
    
    if ($seriesData && isset($seriesData['matchList'])) {
        $currentDateTime = new \DateTime('now', new \DateTimeZone('GMT'));
        $currentDateStr = $currentDateTime->format('Y-m-d');
        
        // Calculate dates for yesterday
        $yesterday = clone $currentDateTime;
        $yesterday->modify('-1 day');
        $yesterdayStr = $yesterday->format('Y-m-d');
        
        // Calculate dates for 3 days ago (for fallback)
        $threeDaysAgo = clone $currentDateTime;
        $threeDaysAgo->modify('-3 days');
        $threeDaysAgoStr = $threeDaysAgo->format('Y-m-d');
        
        // Filter live, today's, and yesterday's matches
        $recentMatches = array_filter($seriesData['matchList'], function($match) use ($currentDateStr, $threeDaysAgoStr) {
            $matchDate = $match['date'] ?? '';
            $matchStarted = $match['matchStarted'] ?? false;
            $matchEnded = $match['matchEnded'] ?? false;
            
            // Include:
            // 1. Live matches (started but not ended)
            // 2. Today's matches (both upcoming and completed)
            // 3. Matches from the last 3 days (as fallback)
            return ($matchStarted && !$matchEnded) || 
                   ($matchDate >= $threeDaysAgoStr && $matchDate <= $currentDateStr);
        });
        
        // Sort matches with priority: Live → Today's Upcoming → Yesterday's
        $recentMatchesArray = array_values($recentMatches);
        usort($recentMatchesArray, function($a, $b) use ($currentDateStr, $yesterdayStr) {
            // Check if matches are live
            $aLive = ($a['matchStarted'] ?? false) && !($a['matchEnded'] ?? false);
            $bLive = ($b['matchStarted'] ?? false) && !($b['matchEnded'] ?? false);
            
            // Priority 1: Live matches first
            if ($aLive && !$bLive) return -1;
            if (!$aLive && $bLive) return 1;
            
            // Get match dates
            $aDate = $a['date'] ?? '';
            $bDate = $b['date'] ?? '';
            
            // Check if matches are today
            $aToday = ($aDate === $currentDateStr);
            $bToday = ($bDate === $currentDateStr);
            
            // Check if matches are yesterday
            $aYesterday = ($aDate === $yesterdayStr);
            $bYesterday = ($bDate === $yesterdayStr);
            
            // Check if matches are upcoming (not started)
            $aUpcoming = !($a['matchStarted'] ?? false);
            $bUpcoming = !($b['matchStarted'] ?? false);
            
            // Priority 2: Today's upcoming matches
            if ($aToday && $aUpcoming && !($bToday && $bUpcoming)) return -1;
            if ($bToday && $bUpcoming && !($aToday && $aUpcoming)) return 1;
            
            // Priority 3: Today's completed matches
            if ($aToday && !$aUpcoming && !($bToday && !$bUpcoming)) return -1;
            if ($bToday && !$bUpcoming && !($aToday && !$aUpcoming)) return 1;
            
            // Priority 4: Yesterday's matches
            if ($aYesterday && !$bYesterday) return -1;
            if ($bYesterday && !$aYesterday) return 1;
            
            // Priority 5: Other dates (most recent first)
            if ($aDate !== $bDate) {
                return strcmp($bDate, $aDate); // Descending order
            }
            
            // If same date, sort by time (most recent first)
            $dateTimeA = strtotime($a['dateTimeGMT'] ?? $aDate);
            $dateTimeB = strtotime($b['dateTimeGMT'] ?? $bDate);
            return $dateTimeB - $dateTimeA;
        });
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'info' => $seriesData['info'] ?? [],
                'matchList' => $recentMatchesArray,
                'total' => count($recentMatchesArray),
                'currentDate' => $currentDateStr
            ]
        ]);
    } else {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to fetch recent matches'
        ])->setStatusCode(500);
    }
}

    /**
 * Fetch detailed scorecard for a specific match
 * @param string $matchId Match ID
 * @return array|null Returns scorecard data or null on failure
 */
public function getMatchScorecard($matchId)
{
    $apiKey = '9bafa7bc-e94e-45de-818e-2cf8c7404f5e';
    $apiUrl = "https://api.cricapi.com/v1/match_scorecard?apikey={$apiKey}&id={$matchId}";
    
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            curl_close($ch);
            log_message('error', '🏏 Scorecard API cURL Error: ' . curl_error($ch));
            return null;
        }
        
        curl_close($ch);
        
        if ($httpCode !== 200) {
            log_message('error', '🏏 Scorecard API HTTP Error: ' . $httpCode);
            return null;
        }
        
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['data'])) {
            log_message('error', '🏏 Scorecard API JSON Error: ' . json_last_error_msg());
            return null;
        }
        
        // // Convert GMT times to IST in scorecard data
        // if (isset($data['data']['dateTimeGMT'])) {
        //     $data['data'] = $this->formatMatchWithIST($data['data']);
        // }
        
        log_message('info', '🏏 Scorecard API: Successfully fetched scorecard for match ' . $matchId);
        
        return $data['data'];
        
    } catch (\Exception $e) {
        log_message('error', '🏏 Scorecard API Exception: ' . $e->getMessage());
        return null;
    }
}

/**
 * API endpoint to get match scorecard (for AJAX calls)
 * @return JSON response
 */
public function apiGetMatchScorecard()
{
    $matchId = $this->request->getGet('id');
    
    if (empty($matchId)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Match ID is not required. Please provide id parameter.',
            'data' => null
        ])->setStatusCode(400);
    }
    
    $scorecardData = $this->getMatchScorecard($matchId);
    
    if ($scorecardData) {
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $scorecardData
        ]);
    } else {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to fetch match scorecard'
        ])->setStatusCode(500);
    }
}

    
}