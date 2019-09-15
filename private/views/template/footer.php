<?php
    $summary = $this->config['footer']['summary']['text'];
    $summary_url = $this->config['footer']['summary']['url'];

    $copyright = $this->config['footer']['copyright'];
    if($summary_url !== null)
    {
        $summary = "<a href=\"{$summary_url}\">{$summary}</a>";
    }

    $socials = '';
    $delimeter = '';
    foreach($this->config['footer']['socials'] as $name => $url)
    {
        $socials .= "{$delimeter}<a href=\"{$url}\" target=\"_blank\" rel=\"noopener noreferrer\">{$name}</a>";
        $delimeter = ' &bull; ';
    }
?>
            </div>
        </div>
        <footer class="footer">
            <div class="container">
                <p><?php echo $summary; ?></p>
                <p style="margin-bottom: 3em;"><?php echo $socials; ?></p>
                <p>&copy; <?php echo date('Y'); ?> <?php echo $copyright;?></p>
                <p>Powered by <a href="https://github.com/grickit/toadstool" target="_blank" rel="noopener noreferrer">Toadstool</a></p>
            </div>
        </footer>

        <!-- This empty script tag is a fix for this nonsense bug in Chrome https://bugs.chromium.org/p/chromium/issues/detail?id=332189 -->
        <!-- Basically, if you have CSS transitions defined, they will fire once as soon as the page loads. It looks really bad. -->
        <!-- For some reason, this prevents it. -->
        <script> </script>
    </body>
</html>