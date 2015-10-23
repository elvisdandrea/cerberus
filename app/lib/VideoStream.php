<?php
/**
 * Class VideoStream
 *
 * Streamer of video files
 */
class VideoStream {

    private $path = "";

    private $stream = "";

    private $buffer = 102400;

    private $start  = -1;

    private $end    = -1;

    private $size   = 0;

    public function __construct($filePath) {

        $this->path = $filePath;
    }

    /**
     * Open stream
     */
    private function open() {
        if (!($this->stream = fopen($this->path, 'rb'))) {
            die('Could not open stream for reading');
        }

    }

    /**
     * Set proper header to serve the video content
     */
    private function setHeader() {
        ob_get_clean();
        Core::setHttpHeader("Content-Type: video/mp4");
        Core::setHttpHeader("Cache-Control: max-age=2592000, public");
        Core::setHttpHeader("Expires: ".gmdate('D, d M Y H:i:s', time()+2592000) . ' GMT');
        Core::setHttpHeader("Last-Modified: ".gmdate('D, d M Y H:i:s', @filemtime($this->path)) . ' GMT' );
        $this->start = 0;
        $this->size  = filesize($this->path);
        $this->end   = $this->size - 1;
        Core::setHttpHeader("Accept-Ranges: 0-".$this->end);

        if (Core::getServerInfo('HTTP_RANGE')) {

            $c_start = $this->start;
            $c_end = $this->end;

            list(, $range) = explode('=', Core::getServerInfo('HTTP_RANGE'), 2);

            if (strpos($range, ',') !== false) {
                Core::setHttpHeader('HTTP/1.1 416 Requested Range Not Satisfiable');
                Core::setHttpHeader("Content-Range: bytes $this->start-$this->end/$this->size");
                exit;
            }
            if ($range == '-') {
                $c_start = $this->size - substr($range, 1);
            } else {
                $range = explode('-', $range);
                $c_start = $range[0];

                $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $c_end;
            }

            $c_end = ($c_end > $this->end) ? $this->end : $c_end;
            if ($c_start > $c_end || $c_start > $this->size - 1 || $c_end >= $this->size) {
                Core::setHttpHeader('HTTP/1.1 416 Requested Range Not Satisfiable');
                Core::setHttpHeader("Content-Range: bytes $this->start-$this->end/$this->size");
                exit;
            }

            $this->start = $c_start;
            $this->end = $c_end;
            $length = $this->end - $this->start + 1;
            fseek($this->stream, $this->start);
            Core::setHttpHeader('HTTP/1.1 206 Partial Content');
            Core::setHttpHeader("Content-Length: ".$length);
            Core::setHttpHeader("Content-Range: bytes $this->start-$this->end/".$this->size);
        } else {
            Core::setHttpHeader("Content-Length: ".$this->size);
        }

    }

    /**
     * close curretly opened stream
     */
    private function end() {
        fclose($this->stream);
        exit;
    }

    /**
     * perform the streaming of calculated range
     */
    private function stream() {
        $i = $this->start;
        set_time_limit(0);
        while(!feof($this->stream) && $i <= $this->end) {
            $bytesToRead = $this->buffer;
            if(($i+$bytesToRead) > $this->end) {
                $bytesToRead = $this->end - $i + 1;
            }
            $data = fread($this->stream, $bytesToRead);
            echo $data;
            flush();
            $i += $bytesToRead;
        }
    }

    /**
     * Start streaming video content
     */
    function start() {
        $this->open();
        $this->setHeader();
        $this->stream();
        $this->end();
    }
}