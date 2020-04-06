require './bootstrap.rb'
require 'mail'

context('Communicate', () => {

  def get_mail
    page.get('alert').contains("The email was output to:"
    page.get('alert').contains("protocol: dummy"
    match = page.get('alert').text.match(/The email was output to: (?<file>\S*)\s/)
    mail = Mail.read(match[:file])
    return mail
  }

  before(function() {
    @test_subject = 'Rspec utilities/communicate test'
    @test_from = 'ellislab.developers.rspec@mailinator.com'
    @test_recipient = 'ellislab.developers@mailinator.com'
    @tmp_path = File.expand_path('support/tmp')
    @mail_files = @tmp_path + '/mail-*'
  }

  beforeEach(function() {
    eeConfig({item: 'mail_protocol', value: 'dummy')
    eeConfig({item: 'dummy_mail_path', value: @tmp_path)

    cy.auth();
    page = Communicate.new
    page.load()

    page.should be_displayed
    page.get('heading').invoke('text').then((text) => { expect(text).to.be.equal('Communicate'
    page.should have_subject
    page.should have_body
    page.should have_mailtype
    page.should have_wordwrap
    page.should have_from_email
    page.should have_attachment
    page.should have_recipient
    page.should have_cc
    page.should have_bcc
    page.should have_member_groups
    page.should have_submit_button
  }

  afterEach(function() {
    FileUtils.rm Dir.glob(@mail_files)
  }

  after(function() {
    eeConfig({item: 'dummy_mail_path', value: '')
  }

  it "shows the Communicate page" do
    page.mailtype.invoke('val').then((val) => { expect(val).to.be.equal('text'
    page.get('wordwrap').should('be.checked')
  }

  it "disables groups with no members" do
    page.member_groups.each do |group|
      group.first(:xpath, ".//..").contains('Guests' if group[:disabled]
    }
  }

  it "shows errors when required fields are not populated" do
    page.from_email.clear().type(''
    page.submit_button.click()

    page.get('alert').should('be.visible')
    page.get('alert_error').should('be.visible')
    page.get('alert').contains("Attention: Email not sent"

    page.subject.first(:xpath, ".//../..")[:class].should include 'invalid'
    page.subject.first(:xpath, ".//..").should have_css 'em.ee-form-error-message'
    page.subject.first(:xpath, ".//..").contains('field is required.'

    page.body.first(:xpath, ".//../..")[:class].should include 'invalid'
    page.body.first(:xpath, ".//..").should have_css 'em.ee-form-error-message'
    page.body.first(:xpath, ".//..").contains('field is required.'

    page.from_email.first(:xpath, ".//../..")[:class].should include 'invalid'
    page.from_email.first(:xpath, ".//..").should have_css 'em.ee-form-error-message'
    page.from_email.first(:xpath, ".//..").contains('field is required.'

    page.submit_button[:value].should eq 'Errors Found'
  }

  it "validates email fields" do
    my_email = 'not an email'

    page.from_email.set my_email
    page.recipient.set my_email
    page.cc.set my_email
    page.bcc.set my_email
    page.submit_button.click()

    page.get('alert').should('be.visible')
    page.get('alert_error').should('be.visible')
    page.get('alert').contains("Attention: Email not sent"

    page.from_emailinvoke('val').then((val) => { expect(val).to.be.equal(my_email
    page.from_email.first(:xpath, ".//../..")[:class].should include 'invalid'
    page.from_email.first(:xpath, ".//..").should have_css 'em.ee-form-error-message'
    page.from_email.first(:xpath, ".//..").contains('field must contain a valid email address.'

    page.recipientinvoke('val').then((val) => { expect(val).to.be.equal(my_email
    page.recipient.first(:xpath, ".//../..")[:class].should include 'invalid'
    page.recipient.first(:xpath, ".//..").should have_css 'em.ee-form-error-message'
    page.recipient.first(:xpath, ".//..").contains('field must contain all valid email addresses.'

    page.ccinvoke('val').then((val) => { expect(val).to.be.equal(my_email
    page.cc.first(:xpath, ".//../..")[:class].should include 'invalid'
    page.cc.first(:xpath, ".//..").should have_css 'em.ee-form-error-message'
    page.cc.first(:xpath, ".//..").contains('field must contain all valid email addresses.'

    page.bccinvoke('val').then((val) => { expect(val).to.be.equal(my_email
    page.bcc.first(:xpath, ".//../..")[:class].should include 'invalid'
    page.bcc.first(:xpath, ".//..").should have_css 'em.ee-form-error-message'
    page.bcc.first(:xpath, ".//..").contains('field must contain all valid email addresses.'
  }

  it "denies multiple email addresses in from field" do
    my_email = 'one@nomail.com,two@nomail.com'

    page.from_email.set my_email
    page.submit_button.click()

    page.get('alert').should('be.visible')
    page.get('alert_error').should('be.visible')
    page.get('alert').contains('Attention: Email not sent'

    page.from_emailinvoke('val').then((val) => { expect(val).to.be.equal(my_email
    page.from_email.first(:xpath, ".//../..")[:class].should include 'invalid'
    page.from_email.first(:xpath, ".//..").should have_css 'em.ee-form-error-message'
    page.from_email.first(:xpath, ".//..").contains('field must contain a valid email address.'
  }

  it "accepts multiple email addresses" do
    my_email = 'one@nomail.com,two@nomail.com'
    page.recipient.set my_email
    page.cc.set my_email
    page.bcc.set my_email
    page.submit_button.click()

    page.recipientinvoke('val').then((val) => { expect(val).to.be.equal(my_email
    page.recipient.first(:xpath, ".//../..")[:class].should_not include 'invalid'
    page.recipient.first(:xpath, ".//..").should_not have_css 'em.ee-form-error-message'
    page.recipient.first(:xpath, ".//..").should_not have_text 'field must contain all valid email addresses.'

    page.ccinvoke('val').then((val) => { expect(val).to.be.equal(my_email
    page.cc.first(:xpath, ".//../..")[:class].should_not include 'invalid'
    page.cc.first(:xpath, ".//..").should_not have_css 'em.ee-form-error-message'
    page.cc.first(:xpath, ".//..").should_not have_text 'field must contain all valid email addresses.'

    page.bccinvoke('val').then((val) => { expect(val).to.be.equal(my_email
    page.bcc.first(:xpath, ".//../..")[:class].should_not include 'invalid'
    page.bcc.first(:xpath, ".//..").should_not have_css 'em.ee-form-error-message'
    page.bcc.first(:xpath, ".//..").should_not have_text 'field must contain all valid email addresses.'
  }

  it "allows recipient to be empty if a group is selected" do
    page.member_groups[0].set(true)
    page.submit_button.click()

    page.recipient.first(:xpath, ".//../..")[:class].should_not include 'invalid'
    page.recipient.first(:xpath, ".//..").should_not have_css 'em.ee-form-error-message'
    page.recipient.first(:xpath, ".//..").should_not have_text 'You left some fields empty.'
  }

//  it "wraps words" do
//    my_subject = @test_subject + ' word wrapping'
//    my_body = "Facillimum id quidem est, inquam. Non est ista, inquam, Piso, magna dissensio. Non autem hoc: igitur ne illud quidem. Sed quid sentiat, non videtis."
#
//    body_wrapped = "Facillimum id quidem est, inquam. Non est ista, inquam, Piso, magna\ndissensio. Non autem hoc: igitur ne illud quidem. Sed quid sentiat, non\nvidetis."
#
//    page.subject.set my_subject
//    page.from_email.set @test_from
//    page.recipient.set @test_recipient
//    page.body.set my_body
//    page.submit_button.click()
#
//    page.get('alert').should('be.visible')
//    page.get('alert_success').should('be.visible')
//    page.get('alert').contains('Your email has been sent'
#
//    mail = get_mail
#
//    mail.subject.should == my_subject
//    mail.from[0].should == @test_from
//    mail.to[0].should == @test_recipient
//    mail.body.decoded.should == body_wrapped + "\n"
//  }
#
//  it "can send a plain text email" do
//    my_subject = @test_subject + ' plain text email'
//    my_body = "This a test email sent from the communicate tool."
#
//    page.subject.set my_subject
//    page.from_email.set @test_from
//    page.recipient.set @test_recipient
//    page.body.set my_body
//    page.submit_button.click()
#
//    page.get('alert').should('be.visible')
//    page.get('alert_success').should('be.visible')
//    page.get('alert').contains('Your email has been sent'
#
//    mail = get_mail
#
//    mail.subject.should == my_subject
//    mail.from[0].should == @test_from
//    mail.to[0].should == @test_recipient
//    mail.body.decoded.should == my_body + "\n"
//  }
#
//  it "can send markdown email" do
//    my_subject = @test_subject + ' markdown email'
//    my_body = "#This is Markdown\n\n[This](https://ellislab.com) is a link.\n**Nice huh?**"
//    html_body = "<h1>This is Markdown</h1>\n\n<p><a href=\"https://ellislab.com\">This</a> is a link.\n<strong>Nice huh?</strong></p>\n"
#
//    page.subject.set my_subject
//    page.from_email.set @test_from
//    page.recipient.set @test_recipient
//    page.body.set my_body
//    page.mailtype.select "Markdown"
//    page.submit_button.click()
#
//    page.get('alert').should('be.visible')
//    page.get('alert_success').should('be.visible')
//    page.get('alert').contains('Your email has been sent'
#
//    mail = get_mail
#
//    mail.subject.should == my_subject
//    mail.from[0].should == @test_from
//    mail.to[0].should == @test_recipient
//    mail.multipart?.should == true
//    mail.parts[0].decoded.should == my_body + "\n\n"
//    mail.parts[1].decoded.should == html_body
//  }
#
//  it "can send html email" do
//    my_subject = @test_subject + ' html email'
//    html_body = "<h1>HTML Email</h1>\n<p>A <strong>strong</strong> <em>emphasis</em> on <a href='http://www.ellislab.com'>anchors</a></p>"
//    plain_body = "This is an HTML email and this is the plaintext alternative"
#
//    page.subject.set my_subject
//    page.from_email.set @test_from
//    page.recipient.set @test_recipient
//    page.body.set html_body
//    page.mailtype.select "HTML"
//    page.wait_until_plaintext_alt_visible
//    page.plaintext_alt.set plain_body
//    page.submit_button.click()
#
//    page.get('alert').should('be.visible')
//    page.get('alert_success').should('be.visible')
//    page.get('alert').contains('Your email has been sent'
#
//    mail = get_mail
#
//    mail.subject.should == my_subject
//    mail.from[0].should == @test_from
//    mail.to[0].should == @test_recipient
//    mail.multipart?.should == true
//    mail.parts[0].decoded.should == plain_body + "\n\n"
//    mail.parts[1].decoded.should == html_body + "\n"
//  }
#
//  it "can send an attachment" do
//    my_subject = @test_subject + ' attachment email'
//    my_body = "This a test email sent from the communicate tool."
#
//    page.subject.set my_subject
//    page.from_email.set @test_from
//    page.recipient.set @test_recipient
//    page.body.set my_body
//    page.attach_file('attachment', 'config.rb')
//    page.submit_button.click()
#
//    page.get('alert').should('be.visible')
//    page.get('alert_success').should('be.visible')
//    page.get('alert').contains('Your email has been sent'
#
//    mail = get_mail
#
//    mail.subject.should == my_subject
//    mail.from[0].should == @test_from
//    mail.to[0].should == @test_recipient
//    mail.multipart?.should == true
//    mail.parts[0].decoded.should == my_body + "\n\n"
//    mail.attachments[0].filename.should == 'config.rb'
//  }
#
//  it "can CC an address" do
//    my_subject = @test_subject + ' CC email'
//    my_body = "This a test email sent from the communicate tool."
#
//    page.subject.set my_subject
//    page.from_email.set @test_from
//    page.recipient.set @test_recipient
//    page.cc.clear().type('ellislab.developers.cc@mailinator.com'
//    page.body.set my_body
//    page.submit_button.click()
#
//    page.get('alert').should('be.visible')
//    page.get('alert_success').should('be.visible')
//    page.get('alert').contains('Your email has been sent'
#
//    mail = get_mail
#
//    mail.subject.should == my_subject
//    mail.from[0].should == @test_from
//    mail.to[0].should == @test_recipient
//    mail.cc[0].should == 'ellislab.developers.cc@mailinator.com'
//    mail.body.decoded.should == my_body + "\n"
//  }
#
//  it "can BCC an address" do
//    my_subject = @test_subject + ' BCC email'
//    my_body = "This a test email sent from the communicate tool."
#
//    page.subject.set my_subject
//    page.from_email.set @test_from
//    page.recipient.set @test_recipient
//    page.bcc.clear().type('ellislab.developers.bcc@mailinator.com'
//    page.body.set my_body
//    page.submit_button.click()
#
//    page.get('alert').should('be.visible')
//    page.get('alert_success').should('be.visible')
//    page.get('alert').contains('Your email has been sent'
#
//    mail = get_mail
#
//    mail.subject.should == my_subject
//    mail.from[0].should == @test_from
//    mail.to[0].should == @test_recipient
//    mail.bcc[0].should == 'ellislab.developers.bcc@mailinator.com'
//    mail.body.decoded.should == my_body + "\n"
//  }
#
//  it "can send to groups" do
//    add_member(username: 'memberone', email: 'ellislab.developers.memberone@mailinator.com')
//    add_member(username: 'membertwo', email: 'ellislab.developers.membertwo@mailinator.com')
//    page.load()
#
//    page.get('wrap').contains("Members (2)"
#
//    my_subject = @test_subject + ' member group email'
//    my_body = "This a test email sent from the communicate tool."
#
//    page.subject.set my_subject
//    page.from_email.set @test_from
//    page.find('input[name="group_5"]').set true
//    page.body.set my_body
//    page.submit_button.click()
#
//    page.get('alert').should('be.visible')
//    page.get('alert_success').should('be.visible')
//    page.get('alert').contains('Total number of emails sent: 2'
#
//    // This isn't ideal as there could be name conflicts but for now
//    // it will have to do since email debug array is being reset with
//    // each call.
//    Dir.glob(@mail_files).count.should == 2
#
//    Dir.glob(@mail_files).each do |file|
//      mail = Mail.read(file)
#
//      mail.subject.should == my_subject
//      mail.from[0].should == @test_from
//      mail.to[0].should match /ellislab.developers.member(one|two)/
//      mail.body.decoded.should == my_body + "\n"
//    }
//  }
#
//  it "can send in batches" do
//    (1..5).each do |n|
//      add_member(username: 'member' + n.toString(), email: 'ellislab.developers.member' + n.toString() + '@mailinator.com')
//    }
//    eeConfig({item: 'email_batchmode', value: 'y')
//    eeConfig({item: 'email_batch_size', value: '4') // Must be less than the number of emails
//    page.load()
#
//    page.get('wrap').contains("Members (5)"
#
//    my_subject = @test_subject + ' batch member group email'
//    my_body = "This a test email sent from the communicate tool."
#
//    page.subject.set my_subject
//    page.from_email.set @test_from
//    page.find('input[name="group_5"]').set true
//    page.body.set my_body
//    page.submit_button.click()
#
//    page.get('alert').should('be.visible')
//    page.get('alert_important').should('be.visible')
//    page.get('alert').contains('The email sending routine will begin in'
#
//    // Manually "follow" the meta-refresh URL
//    meta = page.first(:xpath, "//meta[@http-equiv='refresh']", visible: false)
//    refresh_url = meta[:content].toString().gsub('6; url=', '')
//    visit(page.current_url.gsub(/index.*/, refresh_url))
#
//    // This isn't ideal as there could be name conflicts but for now
//    // it will have to do since email debug array is being reset with
//    // each call.
//    Dir.glob(@mail_files).count.should == 5
#
//    Dir.glob(@mail_files).each do |file|
//      mail = Mail.read(file)
#
//      mail.subject.should == my_subject
//      mail.from[0].should == @test_from
//      mail.to[0].should match /ellislab.developers.member[12345]/
//      mail.body.decoded.should == my_body + "\n"
//    }
//  }
#
//  it "can send attachments in batches" do
//    (1..5).each do |n|
//      add_member(username: 'member' + n.toString(), email: 'ellislab.developers.member' + n.toString() + '@mailinator.com')
//    }
//    eeConfig({item: 'email_batchmode', value: 'y')
//    eeConfig({item: 'email_batch_size', value: '4') // Must be less than the number of emails
//    page.load()
#
//    page.get('wrap').contains("Members (5)"
#
//    my_subject = @test_subject + ' batch member group email'
//    my_body = "This a test email sent from the communicate tool."
#
//    page.subject.set my_subject
//    page.from_email.set @test_from
//    page.find('input[name="group_5"]').set true
//    page.body.set my_body
//    page.attach_file('attachment', 'config.rb')
//    page.submit_button.click()
#
//    page.get('alert').should('be.visible')
//    page.get('alert_important').should('be.visible')
//    page.get('alert').contains('The email sending routine will begin in'
#
//    // Manually "follow" the meta-refresh URL
//    meta = page.first(:xpath, "//meta[@http-equiv='refresh']", visible: false)
//    refresh_url = meta[:content].toString().gsub('6; url=', '')
//    visit(page.current_url.gsub(/index.*/, refresh_url))
#
//    // This isn't ideal as there could be name conflicts but for now
//    // it will have to do since email debug array is being reset with
//    // each call.
//    Dir.glob(@mail_files).count.should == 5
#
//    Dir.glob(@mail_files).each do |file|
//      mail = Mail.read(file)
#
//      mail.subject.should == my_subject
//      mail.from[0].should == @test_from
//      mail.to[0].should match /ellislab.developers.member[12345]/
//      mail.multipart?.should == true
//      mail.parts[0].decoded.should == my_body + "\n\n"
//      mail.attachments[0].filename.should == 'config.rb'
//    }
//  }
}
