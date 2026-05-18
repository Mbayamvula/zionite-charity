-- Add payment tracking columns to donations table
USE zionite_charity;

ALTER TABLE donations
    ADD COLUMN IF NOT EXISTS payment_reference VARCHAR(255) NULL AFTER status,
    ADD COLUMN IF NOT EXISTS transaction_id VARCHAR(255) NULL AFTER payment_reference;
