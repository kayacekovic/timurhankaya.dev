<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::create([
            'title' => 'HTML Elements Demonstration',
            'slug' => 'html-elements-demonstration',
            'excerpt' => 'This is a sample blog post that contains examples of various HTML elements to ensure the typography and styling are working flawlessly.',
            'published_at' => now(),
            'content' => <<<'HTML'
<p>This is a paragraph of text. It demonstrates the basic typography styling provided by the custom CSS properties we've written. The quick brown fox jumps over the lazy dog. Below you will find examples of various HTML structural and stylistic elements used routinely in articles.</p>

<h2>Headings h2-h6</h2>
<h3>This is an H3 heading</h3>
<h4>This is an H4 heading</h4>
<h5>This is an H5 heading</h5>
<h6>This is an H6 heading</h6>

<hr>

<h2>Inline Formatting</h2>
<p>Here we show some common inline text tags: you can make text <strong>bold (strong)</strong>, or <em>italic (em)</em>, or even <u>underlined (u)</u>. Some text might be <mark>highlighted (mark)</mark>. Also, here is a <a href="#">link inside the paragraph</a>.</p>

<h2>Blockquotes</h2>
<blockquote>
<p>Design is not just what it looks like and feels like. Design is how it works.</p>
<cite>— Steve Jobs</cite>
</blockquote>

<h2>Lists</h2>
<p>Below is an example of an unordered list:</p>
<ul>
    <li>First item in the unordered list</li>
    <li>Second item in the unordered list
        <ul>
            <li>Nested list item A</li>
            <li>Nested list item B</li>
        </ul>
    </li>
    <li>Third item in the unordered list</li>
</ul>

<p>And here is an ordered list:</p>
<ol>
    <li>Step 1: Planning and sketching</li>
    <li>Step 2: Prototyping and designing</li>
    <li>Step 3: Implementation and coding
        <ol>
            <li>Writing HTML structure</li>
            <li>Applying Tailwind utilities</li>
        </ol>
    </li>
</ol>

<h2>Code & Preformatted Text</h2>
<p>You can embed inline code like <code>var foo = "bar";</code>, or a whole block of code.</p>

<pre><code>function helloWorld() {
    console.log("Hello, World!");
    return true;
}</code></pre>

<h2>Tables</h2>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Role</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Jane Doe</td>
            <td>Lead Designer</td>
            <td>Active</td>
        </tr>
        <tr>
            <td>John Smith</td>
            <td>Fullstack Developer</td>
            <td>Active</td>
        </tr>
        <tr>
            <td>Alice Walker</td>
            <td>Product Manager</td>
            <td>On Leave</td>
        </tr>
    </tbody>
</table>

<h2>Images</h2>
<img src="https://images.unsplash.com/photo-1542831371-29b0f74f9713?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80" alt="Programming workspace">
<p>The image above demonstrates how images naturally scale, have rounded corners, and shadow effectively within the <code>.blog-content</code> area.</p>
HTML
        ]);
    }
}
