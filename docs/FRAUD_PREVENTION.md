# Fraud Detection & Risk Management

### Mechanisms
- **IP Reputation Check**: Immediate restriction if IP matches `blocked_ips` registry.
- **Duplicate Order Throttling**: Flags identical phone numbers submitting multiple orders in < 15 minutes.
- **District Verification**: Confirms valid postal district combinations against Bangladesh postal data.
- **Manual Review Flag**: High-risk orders marked for administrative telephone verification before dispatch.
