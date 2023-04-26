<div class="wlcform">
  <form class="wlcform__form">
    <div class="wlcform__row">
      <label class="wlcform__label" for="first-name">First Name *</label>
      <input class="wlcform__input" type="text" name="first_name" value="<?php echo $first_name; ?>" required>
    </div>
    <div class="wlcform__row">
      <label class="wlcform__label" for="last-name">Last Name *</label>
      <input class="wlcform__input" type="text" name="last_name" value="<?php echo $last_name; ?>" required>
    </div>
    <div class="wlcform__row">
      <label class="wlcform__label" for="email">Email *</label>
      <input class="wlcform__input wlcform__input--email" type="email" name="email" value="<?php echo $email; ?>" required>
    </div>
    <div class="wlcform__row">
      <label class="wlcform__label" for="subject">Subject *</label>
      <input class="wlcform__input" type="text" name="subject" required>
    </div>
    <div class="wlcform__row">
      <label class="wlcform__label" for="message">Message *</label>
      <textarea class="wlcform__textarea" name="message" rows="5" required></textarea>
    </div>
    <input type="hidden" name="action" value="wlcform_submit">
    <input type="hidden" name="nonce" value="<?php echo $nonce; ?>">
    <button class="wlcform__submit" type="submit">Submit</button>
  </form>
  <div class="wlcform__response"></div>
</div>
