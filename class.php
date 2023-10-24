class ICS {

        public $projectCreator = "Wonder Image";
        public $projectName = "Wonder Image Calendar";
        public $projectVersion = "1.0";
        public $projectLang = "IT";

        public $version = "2.0";
        public $name = "Calendario";
        public $scale = "GREGORIAN";
        public $timezone = "Europe/Rome";
        public $method = "PUBLISH";

        private $file;
        private $events = [];

        public function __construct($calendarName) { $this->name = $calendarName; }

        public function setProject($creator, $name, $version, $lang) {

            $this->projectCreator = $creator;
            $this->projectName = $name;
            $this->projectVersion = $version;
            $this->projectLang = $lang;

        }

        public function setVersion($version) { $this->version = $version; }
        public function setScale($scale) { $this->scale = $scale; }
        public function setTimezone($timezone) { $this->timezone = $timezone; }
        public function setMethod($method) { $this->method = $method; }
        public function createDate($date) { return date("Ymd", strtotime($date))."T".date("His", strtotime($date)); }

        public function newEvent($id, $title, $start, $end, $stamp, $description = '', $position = '', $organizer = '', $frequency = '', $url = '', $conference = '') {

            $event = [];
            $event['UID'] = $id; # Id
            $event['SUMMARY'] = $title; # Titolo
            $event['DTSTART'] = $start; # Inizio evento
            $event['DTEND'] = $end; # Fine evento
            $event['DTSTAMP'] = $stamp; # Creazione
            $event['DESCRIPTION'] = $description; # Descrizione
            $event['LOCATION'] = $position; # Posizione
            $event['URL'] = $url; # Url
            $event['CONFERENCE'] = $conference; # Videochiamata
            $event['RRULE'] = $frequency; # Frequenza
            $event['ORGANIZER'] = $organizer; # Organizzatore
            $event['STATUS'] = "CONFIRMED"; # Stato
            $event['SEQUENCE'] = "3"; 

            array_push($this->events, $event);

            # Allegato
            // $this->events .= "ATTACH:FMTTYPE=application/postscript:ftp://example.com/pub/reports/r-960812.ps\r\n";

            # ND
            // $this->file .= "ACTION:DISPLAY\r\n";

        }
        
        public function export($dir = null) {

            $LINE_BREAK = ($dir === null) ? "<br>" : "\r\n";

            $this->file = "BEGIN:VCALENDAR$LINE_BREAK";
            $this->file .= "PRODID:-//$this->projectCreator//$this->projectName $this->projectVersion//$this->projectLang$LINE_BREAK";
            $this->file .= "VERSION:$this->version$LINE_BREAK";
            $this->file .= "NAME:$this->name$LINE_BREAK";
            $this->file .= "X-WR-CALNAME:$this->name$LINE_BREAK";
            $this->file .= "CALSCALE:$this->scale$LINE_BREAK";
            $this->file .= "METHOD:$this->method$LINE_BREAK";
            
            foreach ($this->events as $k => $events) {

                $this->file .= "BEGIN:VEVENT$LINE_BREAK";

                foreach ($events as $KEY => $VALUE) { 
                    if (!empty($VALUE)) {
                        $this->file .= "$KEY:$VALUE$LINE_BREAK";
                    }
                }

                $this->file .= "END:VEVENT$LINE_BREAK";
                
            }

            $this->file .= "END:VCALENDAR";

            if ($dir === null) {

                echo $this->file;

            } else if ($dir === true) {

                header("Content-type:text/calendar");
                header('Content-Disposition: attachment; filename="'.$this->name.'.ics"');
                Header('Content-Length: '.strlen($this->file));
                Header('Connection: close');

                echo $this->file;

            } else {

                $file = fopen($dir, "w");
                fwrite($file, $this->file);
                fclose($file);

            }

        }

    }
