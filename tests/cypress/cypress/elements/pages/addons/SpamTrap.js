import ControlPanel from '../ControlPanel'

class SpamTrap extends ControlPanel {
    constructor() {
		super()

		this.urlMatch = /addons\/settings\/spam/

		this.elements({

			'keyword_search': 'div.filters input[name=filter_by_keyword]',

			// Main box elements
			'heading': '.align-right div.col.w-16 div.box form h1',
			'perpage_filter': 'div.col.w-16 div.box form h1 + div.filters ul li:first-child',
			'perpage_filter_menu': 'div.col.w-16 div.box form h1 + div.filters ul li:first-child div.sub-menu ul',
			'perpage_manual_filter': 'input[name="perpage"]',

			// Main box's table elements
			'spam_trap': '.ee-main__content form .table-responsive table tr',
			'selected_spam': '.ee-main__content form .table-responsive table tr.selected',

			'content_header': '.ee-main__content form .table-responsive table tr th:first-child',
			'date_header': '.ee-main__content form .table-responsive table tr th:nth-child(2)',
			'ip_header': '.ee-main__content form .table-responsive table tr th:nth-child(3)',
			'type_header': '.ee-main__content form .table-responsive table tr th:nth-child(4)',
			'manage_header': '.ee-main__content form .table-responsive table tr th:nth-child(4)',
			'checkbox_header': '.ee-main__content form .table-responsive table tr th:nth-child(5)',
			'check_all': '.ee-main__content form .table-responsive table tr th:nth-child(5) input',

			'content': '.ee-main__content form .table-responsive table tr td:first-child',
			'date': '.ee-main__content form .table-responsive table tr td:nth-child(2)',
			'ip_addresses': '.ee-main__content form .table-responsive table tr td:nth-child(3)',
			'manage_actions': '.ee-main__content form .table-responsive table tr td:nth-child(4)',

			'bulk_action': 'form fieldset.bulk-action-bar select[name="bulk_action"]',
			'action_submit_button': 'form fieldset.bulk-action-bar button',

			'no_results': 'tr.no-results',

			'view_modal': 'div.spam-modal',
			'view_modal_header': 'div.spam-modal h1'
		})

	}

	load() {
		this.open_dev_menu()
		this.get('main_menu').find('a:contains("Add-Ons")').click()
		this.get('wrap').find('div.tbl-wrap table tr a:contains("Spam")').click()
	}
}
export default SpamTrap;
