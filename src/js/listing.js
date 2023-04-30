(function ($) {
  class WLCListing {
    constructor () {
      this.listing = $('.wlclisting')
      this.entryDetails = $('.wlclisting__entry-details')
      this.pagination = $('.wlclisting__pagination')

      this.currentPage = 1

      this.listing.on('click', '.wlclisting__entry', this.showEntryDetails.bind(this))
      this.pagination.on('click', '.wlclisting__pagination-page', this.loadEntriesPage.bind(this))
    }

    showEntryDetails (event) {
      const entryId = $(event.target).closest('.wlclisting__entry').data('id')
      const self = this
      this.entryDetails.hide().html('')
      $.ajax({
        // eslint-disable-next-line no-undef
        url: wclform.ajaxUrl,
        type: 'post',
        data: {
          action: 'get_entry_details',
          entry_id: entryId,
          // eslint-disable-next-line no-undef
          security: wclform.listing_nonce
        },
        success: function (response) {
          console.log(response)
          self.entryDetails.show().html(response.data)
        },
        error: function (response) {
          console.log(response)
        }
      })
    }

    loadEntriesPage (event) {
      event.preventDefault()
      const page = $(event.target).data('page')
      const totalPages = this.pagination.data('total-pages')
      const visiblePages = 5

      if (page !== this.currentPage) {
        $('.wlclisting__pagination-page').removeClass('current')
        $(event.target).addClass('current')
        this.currentPage = page

        let startPage, endPage
        if (totalPages <= visiblePages) {
          // show all pages
          startPage = 1
          endPage = totalPages
        } else {
          // calculate start and end pages with ellipses
          if (page <= Math.ceil(visiblePages / 2)) {
            startPage = 1
            endPage = visiblePages
          } else if (page + Math.floor(visiblePages / 2) >= totalPages) {
            startPage = totalPages - visiblePages + 1
            endPage = totalPages
          } else {
            startPage = page - Math.floor(visiblePages / 2)
            endPage = page + Math.ceil(visiblePages / 2) - 1
          }
        }

        let paginationHtml = ''
        if (startPage > 1) {
          paginationHtml += '<a href="#" class="wlclisting__pagination-page" data-page="1">1</a>'
          if (startPage > 2) {
            paginationHtml += '<span class="ellipsis">&hellip;</span>'
          }
        }

        for (let i = startPage; i <= endPage; i++) {
          const classString = (i === page) ? 'wlclisting__pagination-page current' : 'wlclisting__pagination-page'
          paginationHtml += `<a href="#" class="${classString}" data-page="${i}">${i}</a>`
        }

        if (endPage < totalPages) {
          if (endPage < totalPages - 1) {
            paginationHtml += '<span class="ellipsis">&hellip;</span>'
          }
          paginationHtml += `<a href="#" class="wlclisting__pagination-page" data-page="${totalPages}">${totalPages}</a>`
        }

        this.pagination.html(paginationHtml)
        this.loadEntries()
      }
    }

    loadEntries () {
      const self = this
      $.ajax({
        // eslint-disable-next-line no-undef
        url: wclform.ajaxUrl,
        type: 'post',
        data: {
          action: 'get_entries',
          page: this.currentPage,
          // eslint-disable-next-line no-undef
          security: wclform.listing_nonce
        },
        success: function (response) {
          self.listing.find('tbody').html(response.data)
        },
        error: function (response) {
          alert(response.responseJSON.data)
        }
      })
    }
  }

  // eslint-disable-next-line no-unused-vars
  const wlcListing = new WLCListing()
})(jQuery)
