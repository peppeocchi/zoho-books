Zoho Books API
==========

## Zoho Books API requests limit

At the time of writing, the API requests limit is 150 requests per minute per organization


## Init

```php
$zb = new ZohoBooks('your authentication token', 'your organization id');
```

The above code will set the API start time

### How to get authtoken and organizationId?

Please refer to the official Zoho Books Documentation
https://www.zoho.com/books/api/v3/

==========

## Contacts

### Get all contacts

```php
$contacts = $zb->allContacts();
```

### Get contact by ID

```php
$contact = $zb->getContact(contact_id);
```

These functions will return a json string if success, false in case of failure

==========

## Invoices

### Get all invoices

```php
$invoices = $zb->allInvoices();
```

You can also set a data range
```php
$invoices = $zb->allInvoices('2014-10-07', '2014-10-08');
```

### Get invoice by ID

```php
$invoice = $zb->getInvoice(invoice_id);
```

These functions will return a json string if success, false in case of failure

### Create an invoice

```php
$zb->postInvoice('invoice_json', true);
```

This function will return true if success, false in case of failure
The second parameter is optional, the default value is false, if set to true will send the invoice to the customer

==========

## Credit notes

### Get all credit notes

```php
$creditNotes = $zb->allCreditNotes();
```

You can also set a data range
```php
$creditNotes = $zb->allCreditNotes();
```

### Get credit note by ID

```php
$creditNote = $zb->getCreditNote(creditNote_id);
```

These functions will return a json string if success, false in case of failure

### Create a credit note

```php
$zb->postCreditNote('creditNote_json');
```

This function will return true if success, false in case of failure

==========

## Response HTTP Code

```php
$httpCode = $zb->getHttpCode();
```

This function will return the response HTTP code after an API call.

==========

## API requests limit

After each call to one of the methods above, a private method check that we haven't reached the Zoho Books limit.
If we've sent 150 requests, the script is sleeped until the end of the minute from the first request.