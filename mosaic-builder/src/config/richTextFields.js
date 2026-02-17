/**
 * Rich text field configuration per tile type.
 * 'inline' = character formatting only, no Enter/paragraphs
 * 'block'  = full formatting including paragraphs, lists, headings, etc.
 */
export const richTextFields = {
  hero:        { title: 'inline', subtitle: 'inline' },
  content:     { heading: 'inline', text: 'block' },
  testimonial: { quote: 'block', author_name: 'inline', author_role: 'inline' },
  pricing:     { plan_name: 'inline', period: 'inline', cta_text: 'inline' },
  counter:     { label: 'inline' },
  iconbox:     { title: 'inline', description: 'block', link_text: 'inline' },
  alert:       { title: 'inline', message: 'block' },
  team:        { name: 'inline', role: 'inline', bio: 'block' },
  image:       { caption: 'inline' },
  video:       { caption: 'inline' },
  headline:    { heading: 'inline', subtitle: 'inline' },
  overlay:     { title: 'inline', description: 'block' },
};

export function getRichTextMode(tileType, fieldKey) {
  return richTextFields[tileType]?.[fieldKey] || null;
}
