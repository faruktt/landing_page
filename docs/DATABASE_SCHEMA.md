# Database Schema & Entity Relationships

### Core Entities
- **orders**: Primary transaction log storing customer address, items, payment status, delivery charges, and courier IDs.
- **products**: Product catalog, prices, promotional offers, and gallery image mappings.
- **courier_settings**: Credentials and webhook tokens for logistics partners.
- **blocked_ips**: Blacklisted client IPs flagged by fraud detection algorithms.
- **settings**: Global site parameters including delivery charges inside/outside Dhaka.

<!-- Note: 2026-01-09 - Order index optimization documented -->

<!-- Note: 2026-02-07 - Order index optimization documented -->

<!-- Note: 2026-02-10 - Order index optimization documented -->

<!-- Note: 2026-02-13 - Order index optimization documented -->
