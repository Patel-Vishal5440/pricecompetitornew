<div class="atbd-mailCompose atbd-mailCompose--position">
    <form action="#">
        <div
            class="atbd-mailCompose__header d-flex justify-content-between align-items-center">
            <h6 class="mailCompose-title">New Message</h6>
            <div class="atbd-mailCompose__action">
                <a href="#">
                    <span data-feather="maximize-2"></span></a>
                <a class="compose-close" href="#" data-trigger="compose">
                    <span data-feather="x"></span></a>
            </div>
        </div>
        <!-- ends: .atbd-mailCompose__header -->
        <div class="atbd-mailCompose__body">
            <div class="mailCompose-form-content">
                <div class="form-group positon-relative">
                    <select name="mail-to" id="mail-to" class="form-control-lg"
                        multiple="multiple">
                        <option value="01">demo@example.com</option>
                        <option value="02">test@example.com</option>
                        <option value="03">xxx@example.com</option>
                    </select>
                    <span class="input-label">To</span>
                </div>
                <div class="form-group positon-relative">
                    <input type="text" class="form-control-lg" name="mail-to"
                        placeholder="Subject">
                </div>
                <div class="form-group">
                    <textarea name="message" id="mail-message"
                        class="form-control-lg"
                        placeholder="Type your message..."></textarea>
                </div>
            </div>
        </div>
        <!-- ends: .atbd-mailCompose__body -->
        <div
            class="atbd-mailCompose__footer d-flex justify-content-between align-items-center">
            <div class="footer-left d-flex align-items-center">
                <button class="btn btn-md btn-primary">Send</button>
                <a href="#">
                    <span data-feather="paperclip"></span></a>
                <a href="#">
                    <span data-feather="smile"></span></a>
            </div>
            <div class="footer-right">
                <a href="#" class="btn-remove">
                    <span data-feather="trash-2"></span></a>
            </div>
        </div>
        <!-- ends: .atbd-mailCompose__footer -->
    </form>
</div>