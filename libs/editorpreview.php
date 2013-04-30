<?php
//global $wp_admin_bar;
global $footerOutput, $headerscripts, $javascript, $phpincludes, $contentPrefilters;

$preheaderscripts = '';
/*$preheaderscripts .= "html, div, span, applet, object, iframe,h1, h2, h3, h4, h5, h6, p, blockquote, pre,a, abbr, acronym, address, big, cite, code,del, dfn, em, img, ins, kbd, q, s, samp,small, strike, strong, sub, sup, tt, var,b, u, i, center,dl, dt, dd, ol, ul, li,fieldset, form, label, legend,table, caption, tbody, tfoot, thead, tr, th, td,article, aside, canvas, details, embed, figure, figcaption, footer, header, hgroup, menu, nav, output, ruby, section, summary,time, mark, audio, video {margin: 0;padding: 0;border: 0;font-size: 100%;font: inherit;vertical-align: baseline;}
article, aside, details, figcaption, figure,footer, header, hgroup, menu, nav, section {display: block;}
body {line-height: 1;padding:20px;}
ol, ul {list-style: none;}
blockquote, q {quotes: none;}
blockquote:before, blockquote:after,q:before, q:after {content: '';content: none;}
table{border-collapse: collapse;border-spacing: 0;}";
*/

$shortcode = stripslashes_deep(strtolower($_GET['code']));
$used = msc_getUsedShortcodes('['.$shortcode.']', array(), true, true);
$elements = get_option('CE_ELEMENTS');
if(!empty($elements)){
    foreach ($elements as $element => $options){
        if(!empty($options['shortcode'])){        
            foreach($used[2] as $currentKey=>$currentShortcode){
                if($options['shortcode'] == strtolower($used[2][$currentKey])){
                    if(!empty($used[3][0])){
                        $atts[$currentKey] = shortcode_parse_atts($used[3][$currentKey]);
                    }else{
                        $atts[$currentKey] = array();
                    }
                    $IDs[$currentKey] = $element;
                    $shortcodes[$currentKey] = $options['shortcode'];
                    $cfg = get_option($element);
                }
            }
        }
    }
}
if(!empty($IDs)){
    foreach($IDs as $currentKey=>$ID){        
        $instance[$currentKey] = msc_getDefaultAtts($ID, $atts[$currentKey]);
        msc_processHeaders($ID, $instance[$currentKey]);
    }
    if(isset($cfg['_defaultContent'])){
        $content = $cfg['_defaultContent'];
    }else{
        $content = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus et feugiat eros. Praesent ac justo orci, quis ornare mi. Quisque dictum eleifend diam, eu congue augue congue ultrices. Proin convallis auctor neque, semper luctus libero pulvinar vel.";
    }
    $outPutCode = msc_doShortcode($instance[0]['atts'], $content, $shortcodes[0]);
    
    $scriptsSize = msc_getscriptsize();    
    
        $scriptsSize = $scriptsSize+strlen($footerOutput)+strlen($outPutCode)+strlen($headerscripts);    
        global $wp_scripts;
        if(!empty($cfg['_assetURL'])){
            foreach($cfg['_assetURL'] as $key=>$asset){
                if(!isset($wp_scripts->registered[$key])){
                    $scriptsSize = msc_getfilesize($asset)+$scriptsSize;
                }
            }
        }
    if($scriptsSize > 0){
        $units = array( 0 => 'B', 1 => 'kB', 2 => 'MB', 3 => 'GB' );
        $log = log($scriptsSize, 1024 );
        $power = (int) $log;
        $size = pow(1024, $log - $power);
        $totalScriptSize = round($size) . $units[$power];
        $headerscripts = $preheaderscripts.$headerscripts;
    }else{
        $totalScriptSize = '0B';
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
        <title>Element Preview</title>       
        <?php wp_head(); ?>
    </head>
    <body class="previewCanvas">
        <?php
        if(!empty($outPutCode)){
            echo $outPutCode;
        }
        ?>
        <?php wp_footer(); ?>
        <script>
            if(parent.document.getElementById('previewscriptsize')){
                parent.document.getElementById('previewscriptsize').innerHTML = '<?php echo $totalScriptSize; ?>';
            }
        
        </script>
    </body>
</html>