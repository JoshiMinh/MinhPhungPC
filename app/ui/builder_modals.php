<!-- Component Selection Modal -->
<div class="modal fade" id="componentModal" tabindex="-1" aria-labelledby="componentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="componentModalLabel">Select Component</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body text-center" style="max-height: 400px; overflow-y: auto;">
                <div id="modalItemContainer" class="d-flex flex-column justify-content-start"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="confirmSelect">Confirm Selection</button>
            </div>
        </div>
    </div>
</div>

<!-- Image Viewing Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Component Image" class="img-fluid" style="max-width: 100%; height: auto;">
            </div>
        </div>
    </div>
</div>
