(function ($) {
  class WLCForm {
    constructor (formSelector, responseSelector) {
      this.form = $(formSelector)
      this.response = $(responseSelector)
      this.submitBtn = this.form.find('.wlcform__submit')

      this.form.submit(this.onSubmit.bind(this))
      this.submitBtn.click(this.validate.bind(this))
    }

    onSubmit (e) {
      // Prevent the form from submitting normally
      e.preventDefault()

      // Disable the submit button and validate the form
      this.submitBtn.prop('disabled', true)
      const isValid = this.validate()

      if (!isValid) return false

      // Serialize form data and send it via AJAX
      const formData = this.form.serialize()
      $.ajax({
        // eslint-disable-next-line no-undef
        url: wclform.ajaxUrl,
        type: 'post',
        data: formData,
        success: this.onSuccess.bind(this),
        error: this.onError.bind(this)
      })
    }

    validate () {
      // Get form data and validate fields
      const firstNameEl = this.form.find('input[name=first_name]')
      const firstName = firstNameEl.val()
      const lastNameEl = this.form.find('input[name=last_name]')
      const lastName = lastNameEl.val()
      const emailEl = this.form.find('input[name=email]')
      const email = emailEl.val()
      const subjectEl = this.form.find('input[name=subject]')
      const subject = subjectEl.val()
      const messageEl = this.form.find('textarea[name=message]')
      const message = messageEl.val()
      let isValid = true

      if (firstName === '') {
        firstNameEl.addClass('error')
        isValid = false
      } else {
        firstNameEl.removeClass('error')
      }

      if (lastName === '') {
        lastNameEl.addClass('error')
        isValid = false
      } else {
        lastNameEl.removeClass('error')
      }

      if (email === '') {
        emailEl.addClass('error')
        isValid = false
      } else if (!this.isValidEmail(email)) {
        emailEl.addClass('error')
        isValid = false
      } else {
        emailEl.removeClass('error')
      }

      if (subject === '') {
        subjectEl.addClass('error')
        isValid = false
      } else {
        subjectEl.removeClass('error')
      }

      if (message === '') {
        messageEl.addClass('error')
        isValid = false
      } else {
        messageEl.removeClass('error')
      }

      // Show an error message if the form is invalid
      if (!isValid) {
        this.response.html('Please check your form for errors and try again')
        this.submitBtn.prop('disabled', false)
      }

      return isValid
    }

    onSuccess (data, status) {
      if (data.success) {
        this.form.hide()
      }

      this.response.html(data.data)

      // Re-enable the submit button
      this.submitBtn.prop('disabled', false)
    }

    onError (xhr, status, error) {
      console.log(xhr)
      console.log(status)
      console.log(error)
      this.response.html(error)

      // Re-enable the submit button
      this.submitBtn.prop('disabled', false)
    }

    isValidEmail (email) {
      const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
      return emailRegex.test(email)
    }
  }

  // eslint-disable-next-line no-unused-vars
  const wlcForm = new WLCForm('.wlcform__form', '.wlcform__response')
})(jQuery)
