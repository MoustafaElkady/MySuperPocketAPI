
<!--footer begin-->
<footer>
    <div id="copyrights">
        <p>&copy; <?php echo date("Y"); ?></p>
    </div>
</footer>


<?php echo $this->loadJSFile($this->getConfig("URL") . "/statics/components/jquery/jquery.min.js"); ?>
<?php echo $this->loadJSFile($this->getConfig("URL") . "/statics/semantic/ui/dist/semantic.min.js"); ?>

<script>
    $(document).ready(function(){
        $('.ui.dropdown')
            .dropdown()
        ;

        $('.ok.label')
            .popup({
                content:'这个传感器正常工作。'
            })
        ;

        $('.error.label')
            .popup({
                content:'哎呀，这个传感器异常。'
            })
        ;
    });
</script>

</body>
</html>