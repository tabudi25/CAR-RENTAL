-- Add down_payment_amount column to bookings table if it doesn't exist
ALTER TABLE bookings 
ADD COLUMN IF NOT EXISTS down_payment_amount DECIMAL(10,2) NULL AFTER total_price;

-- Update existing bookings that have payment_status but no down_payment_amount
-- This is a one-time fix for existing data
UPDATE bookings 
SET down_payment_amount = total_price * 0.5 
WHERE payment_status = 'pending' 
  AND payment_method IS NOT NULL 
  AND payment_reference IS NOT NULL 
  AND (down_payment_amount IS NULL OR down_payment_amount = 0);

