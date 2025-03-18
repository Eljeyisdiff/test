<div class="windows window-up">
    <div class="window">
        <p>Window <?php echo htmlspecialchars($windowNumber);?></p>
        <h2>Currently Serving...</h2>
    </div>
    <div class="now-serving">
        <p><?php echo htmlspecialchars($currentTicket ?: '-');?></p>
    </div>
</div>