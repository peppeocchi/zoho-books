Zoho Books API
==========

## Zoho Books API requests limit

At the time of writing, the API requests limit is 150 requests per minute per organization


## Init

```php
$zb = new ZohoBooks('your authentication token', 'your organization id');
```

The above code will set the API start time

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