/* =============================================================================
   Block Advisory: settings shared by every development page.
   Change a phone number, the agent or where leads are sent here, once.
   ========================================================================== */
window.BLOCK_AGENCY = {
  name: 'Block Advisory',
  site: 'https://block.ke',
  logo: 'https://block.ke/wp-content/uploads/2026/09/block-advisory-horizontal.png',
  logoLight: 'https://block.ke/wp-content/uploads/2026/08/block-advisory-horizontal-white-1.png',

  phone: '+254725937686',          // used for tel: links
  phoneDisplay: '0725 937 686',
  whatsapp: '254725937686',        // digits only, used for wa.me links
  email: 'sales@block.ke',
  address: 'Upper Hill Gardens, Suite D15, Upper Hill, Nairobi',

  agent: {
    name: 'Loyd Mokaya',
    role: 'Founder & Lead Agent',
    photo: 'https://block.ke/wp-content/uploads/2026/02/IMG_3002-scaled.jpeg'
  },

  // Where enquiries go. POSTs JSON (name, phone, email, development, unit, UTM tags...).
  // Works with a Zapier/Make webhook, Formspree, or a small endpoint that creates a Zoho CRM lead.
  // Leave empty to hand each enquiry to WhatsApp with the details pre-typed.
  formEndpoint: '',

  // "Why buy with Block Advisory": shown on every development page.
  why: [
    { title: 'Independent advice', text: 'We compare developments, layouts and payment plans side by side, so you choose with clarity rather than pressure.' },
    { title: 'Checks before you commit', text: 'We help you review the developer, approvals and sale documents before you reserve or pay a deposit.' },
    { title: 'One advisor to handover', text: 'From reservation and payments to construction updates and handover, the same advisor stays with you.' }
  ],

  disclaimer: 'All information is provided in good faith from the developer\'s published material and is subject to change without notice. Prices, sizes, availability, payment terms and completion dates should be confirmed before you commit. Images may be artist\'s impressions. Block Advisory does not provide financial or legal advice.'
};
