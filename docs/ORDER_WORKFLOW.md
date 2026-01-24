# Order Processing Workflow

1. **Submission**: Customer fills single-page checkout form on landing page.
2. **Pre-check**: IP and Phone validation against blocked list.
3. **Draft / Incomplete**: Captures partial submissions to recover abandoned carts.
4. **Confirmed**: Order placed, inventory allocated, notification dispatched.
5. **Courier Handover**: Automated consignment generation via Courier API.
6. **Delivery & Settlement**: Webhook tracking updates order status to completed or returned.

<!-- Note: 2026-01-05 - Consignment state transition reviewed -->

<!-- Note: 2026-01-24 - Consignment state transition reviewed -->
